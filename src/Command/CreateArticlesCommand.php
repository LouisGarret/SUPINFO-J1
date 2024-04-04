<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use App\Event\ArticlePublishedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:create:articles',
    description: 'Import articles',
)]
class CreateArticlesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        $response = $response->toArray();

        $progressBar = new ProgressBar($output, count($response));
        $progressBar->start();

        foreach ($response as $post) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(
                ['email' => sprintf('email%s@email.com', random_int(0, 20))]
            );

            if (!$user) {
                continue;
            }

            $article = new Article();
            $article->setTitle($post['title'])
                ->setContent($post['body'])
                ->setStatus(Article::AVAILABLE_STATUS[random_int(0, 1)])
                ->setAuthor($user);

            $this->entityManager->persist($article);

            if ($article->getStatus() === Article::STATUS_PUBLISHED) {
                $this->eventDispatcher->dispatch(new ArticlePublishedEvent($article));
            }

            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}

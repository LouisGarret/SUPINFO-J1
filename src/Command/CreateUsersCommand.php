<?php

namespace App\Command;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommand(
    name: 'app:create:users',
    description: 'Add a short description for your command',
)]
class CreateUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserFactory $userFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('number', InputArgument::REQUIRED, 'Number of users to create');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $numberOfUser = $input->getArgument('number');
        $progressBar = new ProgressBar($output, $numberOfUser);
        $progressBar->start();

        for ($i = 0; $i < $numberOfUser; $i++) {
            $user = $this->userFactory->create(
                sprintf('Prénom %s', $i),
                sprintf('Nom %s', $i),
                sprintf('email%s@email.com', $i),
                sprintf('Username %s', $i),
                'test'
            );

            $this->entityManager->persist($user);
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $io->success(sprintf('Success : %s users created', $numberOfUser));

        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create:users',
    description: 'Add a short description for your command',
)]
class CreateUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
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
            $user = new User();
            $user->setFirstName(sprintf('Prénom %s', $i))
                ->setLastName(sprintf('Nom %s', $i))
                ->setEmail(sprintf('email%s@email.com', $i))
                ->setUsername(sprintf('Username %s', $i));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));

            $this->entityManager->persist($user);
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $io->success(sprintf('Success : %s users created', $numberOfUser));

        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Utils\Utils;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
            private EntityManagerInterface $entityManager,
            private UserRepository $userRepository,
            private Utils $utils,
            private UserPasswordHasherInterface
            $userPasswordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('pincode', InputArgument::REQUIRED, 'The pincode of the user.')
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a new user.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user. Enter as argument username and 6 digits pincode.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
            $user = new User();
            $pincode = $input->getArgument('pincode');
            $user->setUsername($input->getArgument('username'));
            $user->setPincode($pincode);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $pincode));
            $user->setRoles(["ROLE_ADMIN"]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            $output->writeln('User '.$user->getUsername().' with pincode '.$user->getPincode().' created');
            return Command::SUCCESS;
    }
}
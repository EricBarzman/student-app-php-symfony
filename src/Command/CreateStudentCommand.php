<?php

namespace App\Command;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-student')]
class CreateStudentCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StudentRepository $studentRepository,
        private Utils $utils
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('firstname', InputArgument::REQUIRED, 'The first name of the student.')
            ->addArgument('lastname', InputArgument::REQUIRED, 'The last name of the student.')
            ->addArgument('gender', InputArgument::REQUIRED, 'The gender of the student.')
            ->setDescription('Creates a new student.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a student. Enter as argument first name, last name, gender, DOB, and class ID.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $student = new Student();
        $student->setFirstname($input->getArgument('firstname'));
        $student->setLastname($input->getArgument('lastname'));
        $student->setGender($input->getArgument('gender'));
        // Set birthdate
        // Set class ???
        return Command::SUCCESS;
    }
}
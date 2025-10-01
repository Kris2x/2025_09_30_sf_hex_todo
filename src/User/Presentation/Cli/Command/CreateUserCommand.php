<?php

namespace App\User\Presentation\Cli\Command;

use App\User\Application\UseCase\CreateUser\CreateUserHandler;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\User\Application\UseCase\CreateUser\CreateUserCommand as CreateUserApplicationCommand;

class CreateUserCommand extends Command
{

    protected static $defaultName = 'app:user-create';

    public function __construct(private readonly CreateUserHandler $handler)
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Enter the email of the user');
        $firstName = $io->ask('Enter the first name of the user');
        $lastName = $io->ask('Enter the last name of the user');

        try {
            $command = new CreateUserApplicationCommand($email, $firstName, $lastName);
            $user = $this->handler->handle($command);

            $io->success(sprintf('User "%s" created successfully with ID: %s', $user->getEmail(), $user->getId()));
        } catch (Exception $e) {
            $io->error('Error creating user: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}

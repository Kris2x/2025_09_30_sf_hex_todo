<?php

namespace App\User\Presentation\Cli\Command;

use App\User\Application\UseCase\DeleteUser\DeleteUserCommand as DeleteUserApplicationCommand;
use App\User\Application\UseCase\DeleteUser\DeleteUserHandler;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteUserCommand extends Command
{
    protected static $defaultName = 'app:user-delete';

    public function __construct(private readonly DeleteUserHandler $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Delete a user')
            ->addArgument('email', InputArgument::REQUIRED, 'User email to delete')
            ->setHelp('This command allows you to delete a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        if (!$io->confirm(sprintf('Are you sure you want to delete user "%s"?', $email), false)) {
            $io->info('User deletion cancelled.');
            return Command::SUCCESS;
        }

        try {
            $command = new DeleteUserApplicationCommand($email);
            $this->handler->handle($command);

            $io->success(sprintf('User with email "%s" has been deleted.', $email));
            return Command::SUCCESS;
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

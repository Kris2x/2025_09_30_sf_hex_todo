<?php

namespace App\User\Presentation\Cli\Command;

use App\User\Application\UseCase\GetAllUsers\GetAllUsersHandler;
use App\User\Application\UseCase\GetAllUsers\GetAllUsersQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetUsersCommand extends Command
{
    protected static $defaultName = 'app:users';

    public function __construct(private readonly GetAllUsersHandler $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Get all users')
            ->setHelp('This command allows you to fetch and display all users from the system.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $query = new GetAllUsersQuery();
        $users = $this->handler->handle($query);

        if (empty($users)) {
            $io->warning('No users found.');
            return Command::SUCCESS;
        }

        $io->title('List of Users');

        $table = new Table($output);
        $table->setHeaders(['ID', 'Email', 'First name', 'Last name']);

        foreach ($users as $user) {
            $table->addRow([
                $user->getId(),
                $user->getEmail(),
                $user->getFirstName(),
                $user->getLastName(),
            ]);
        }

        $table->render();

        $io->newLine();
        $io->success(sprintf('Total users: %d', count($users)));

        return Command::SUCCESS;
    }
}

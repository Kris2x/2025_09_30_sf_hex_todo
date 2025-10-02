<?php

namespace App\Task\Presentation\Cli\Command;

use App\Task\Application\Command\AssignTask\AssignTaskCommand as AssignTaskApplicationCommand;
use App\Task\Application\Command\AssignTask\AssignTaskHandler;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Application\Query\GetAllUsers\GetAllUsersHandler;
use App\User\Application\Query\GetAllUsers\GetAllUsersQuery;
use DomainException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssignTaskCommand extends Command
{
    protected static $defaultName = 'app:task-assign';

    public function __construct(
        private readonly AssignTaskHandler $assignTaskHandler,
        private readonly GetAllUsersHandler $getAllUsersHandler,
        private readonly TaskRepositoryInterface $taskRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Assign a task to a user')
            ->addArgument('task-id', InputArgument::REQUIRED, 'Task ID to assign')
            ->setHelp('This command allows you to assign a task to a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $taskId = $input->getArgument('task-id');

        // Verify task exists
        $task = $this->taskRepository->findById($taskId);
        if ($task === null) {
            $io->error(sprintf('Task with id "%s" not found', $taskId));
            return Command::FAILURE;
        }

        $io->title(sprintf('Assigning task: "%s"', $task->getTitle()));

        // Get all users
        $query = new GetAllUsersQuery();
        $users = $this->getAllUsersHandler->handle($query);

        if (empty($users)) {
            $io->warning('No users found in the system. Please create users first.');
            return Command::FAILURE;
        }

        // Build choices array
        $choices = ['None (unassign)' => null];
        foreach ($users as $user) {
            $label = sprintf('%s %s (%s)', $user->getFirstName(), $user->getLastName(), $user->getEmail());
            $choices[$label] = $user->getId();
        }

        // Ask user to select
        $selectedLabel = $io->choice('Select user to assign', array_keys($choices));
        $userId = $choices[$selectedLabel];

        try {
            $command = new AssignTaskApplicationCommand($taskId, $userId);
            $this->assignTaskHandler->handle($command);

            if ($userId === null) {
                $io->success(sprintf('Task "%s" has been unassigned', $task->getTitle()));
            } else {
                $io->success(sprintf('Task "%s" has been assigned to %s', $task->getTitle(), $selectedLabel));
            }

            return Command::SUCCESS;
        } catch (DomainException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

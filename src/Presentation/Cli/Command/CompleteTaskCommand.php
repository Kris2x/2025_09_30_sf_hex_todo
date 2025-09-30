<?php

namespace App\Presentation\Cli\Command;

use App\Domain\Port\TaskRepositoryInterface;
use DomainException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompleteTaskCommand extends Command
{
    protected static $defaultName = 'app:complete-task';

    public function __construct(private readonly TaskRepositoryInterface $taskRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Mark a task as completed')
            ->addArgument('id', InputArgument::REQUIRED, 'Task ID to complete')
            ->setHelp('This command allows you to mark a task as completed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $taskId = $input->getArgument('id');

        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            $io->error(sprintf('Task with ID "%s" not found.', $taskId));
            return Command::FAILURE;
        }

        try {
            $task->complete();
            $this->taskRepository->save($task);

            $io->success(sprintf('Task "%s" marked as completed!', $task->getTitle()));
            return Command::SUCCESS;
        } catch (DomainException $e) {
            $io->warning($e->getMessage());
            return Command::FAILURE;
        }
    }
}

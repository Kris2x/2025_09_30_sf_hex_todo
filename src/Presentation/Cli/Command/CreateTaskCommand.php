<?php

namespace App\Presentation\Cli\Command;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTaskCommand extends Command
{
    protected static string $defaultName = 'app:create-task';

    public function __construct(private readonly TaskRepositoryInterface $taskRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Creates a new task')
            ->setHelp('This command allows you to create a task...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Write your task title:');
        $title = trim(fgets(STDIN));

        $output->writeln('Write your task description:');
        $description = trim(fgets(STDIN));

        $task = new Task($title, $description);
        $this->taskRepository->save($task);
        $output->writeln('Task created successfully with ID: ' . $task->getId());

        return Command::SUCCESS;
    }
}

<?php

namespace App\Presentation\Cli\Command;

use App\Infrastructure\Persistence\Doctrine\DoctrineTaskRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetTasksCommand extends Command
{
    protected static string $defaultName = 'app:get-tasks';

    public function __construct(private readonly DoctrineTaskRepository $taskRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Get all tasks')
            ->setHelp('This command allows you to get all tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tasks = $this->taskRepository->findAll();

        $tasksArr = array_map(fn($task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'isCompleted' => $task->isCompleted(),
        ], $tasks);

        if (empty($tasksArr)) {
            $output->writeln('No tasks found.');
            return Command::SUCCESS;
        }

        foreach ($tasksArr as $task) {
            $output->writeln(sprintf(
                'ID: %s, Title: %s, Description: %s, Completed: %s',
                $task['id'],
                $task['title'],
                $task['description'],
                $task['isCompleted'] ? 'Yes' : 'No'
            ));
        }

        return Command::SUCCESS;
    }
}

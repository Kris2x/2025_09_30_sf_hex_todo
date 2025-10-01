<?php

namespace App\Task\Presentation\Cli\Command;

use App\Task\Application\UseCase\GetAllTasks\GetAllTasksHandler;
use App\Task\Application\UseCase\GetAllTasks\GetAllTasksQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetTasksCommand extends Command
{
    protected static $defaultName = 'app:get-tasks';

    public function __construct(private readonly GetAllTasksHandler $handler)
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
        $io = new SymfonyStyle($input, $output);

        $query = new GetAllTasksQuery();
        $tasks = $this->handler->handle($query);

        if (empty($tasks)) {
            $io->warning('No tasks found.');
            return Command::SUCCESS;
        }

        $io->title('📋 Task List');

        $table = new Table($output);
        $table->setHeaders(['ID', 'Title', 'Description', 'Status']);

        foreach ($tasks as $task) {
            $status = $task->isCompleted()
                ? '<fg=green>✓ Completed</>'
                : '<fg=yellow>○ Pending</>';

            $table->addRow([
                $task->getId(),
                $task->getTitle(),
                $task->getDescription(),
                $status
            ]);
        }

        $table->render();

        $io->newLine();
        $io->success(sprintf('Total: %d task(s)', count($tasks)));

        return Command::SUCCESS;
    }
}

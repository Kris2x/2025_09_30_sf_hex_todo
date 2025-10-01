<?php

namespace App\Task\Presentation\Cli\Command;

use App\Task\Application\UseCase\CreateTask\CreateTaskCommand as CreateTaskApplicationCommand;
use App\Task\Application\UseCase\CreateTask\CreateTaskHandler;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateTaskCommand extends Command
{
    protected static $defaultName = 'app:task-create';

    public function __construct(private readonly CreateTaskHandler $handler)
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
        $io = new SymfonyStyle($input, $output);

        $title = $io->ask('Task title');
        $description = $io->ask('Task description (optional)', '');

        try {
            $command = new CreateTaskApplicationCommand($title, $description);
            $task = $this->handler->handle($command);

            $io->success(sprintf('Task "%s" created successfully with ID: %s', $task->getTitle(), $task->getId()));
            return Command::SUCCESS;
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

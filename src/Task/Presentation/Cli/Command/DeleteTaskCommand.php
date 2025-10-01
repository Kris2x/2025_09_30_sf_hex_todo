<?php

namespace App\Task\Presentation\Cli\Command;

use App\Task\Application\UseCase\DeleteTask\DeleteTaskCommand as DeleteTaskApplicationCommand;
use App\Task\Application\UseCase\DeleteTask\DeleteTaskHandler;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteTaskCommand extends Command
{
    protected static $defaultName = 'app:task-delete';

    public function __construct(private readonly DeleteTaskHandler $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Delete a task')
            ->addArgument('id', InputArgument::REQUIRED, 'Task ID to delete')
            ->setHelp('This command allows you to delete a task');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $taskId = $input->getArgument('id');

        if (!$io->confirm(sprintf('Are you sure you want to delete task "%s"?', $taskId), false)) {
            $io->info('Task deletion cancelled.');
            return Command::SUCCESS;
        }

        try {
            $command = new DeleteTaskApplicationCommand($taskId);
            $this->handler->handle($command);

            $io->success(sprintf('Task with ID "%s" has been deleted.', $taskId));
            return Command::SUCCESS;
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

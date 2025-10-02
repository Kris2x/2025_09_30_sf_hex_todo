<?php

namespace App\Task\Presentation\Cli\Command;

use App\Task\Application\Command\CompleteTask\CompleteTaskCommand as CompleteTaskApplicationCommand;
use App\Task\Application\Command\CompleteTask\CompleteTaskHandler;
use DomainException;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompleteTaskConsoleCommand extends Command
{
    protected static $defaultName = 'app:task-complete';

    public function __construct(private readonly CompleteTaskHandler $handler)
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

        try {
            $command = new CompleteTaskApplicationCommand($taskId);
            $task = $this->handler->handle($command);

            $io->success(sprintf('Task "%s" marked as completed!', $task->getTitle()));
            return Command::SUCCESS;
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (DomainException $e) {
            $io->warning($e->getMessage());
            return Command::FAILURE;
        }
    }
}

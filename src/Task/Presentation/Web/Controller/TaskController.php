<?php

namespace App\Task\Presentation\Web\Controller;

use App\Task\Application\Command\CompleteTask\CompleteTaskCommand;
use App\Task\Application\Command\CompleteTask\CompleteTaskHandler;
use App\Task\Application\Command\CreateTask\CreateTaskCommand;
use App\Task\Application\Command\CreateTask\CreateTaskHandler;
use App\Task\Application\Command\DeleteTask\DeleteTaskCommand;
use App\Task\Application\Command\DeleteTask\DeleteTaskHandler;
use App\Task\Application\Query\GetAllTasks\GetAllTasksHandler;
use App\Task\Application\Query\GetAllTasks\GetAllTasksQuery;
use App\Task\Application\Query\GetMyTasks\GetMyTasksHandler;
use App\Task\Application\Query\GetMyTasks\GetMyTasksQuery;
use App\User\Application\Query\GetAllUsers\GetAllUsersHandler;
use App\User\Application\Query\GetAllUsers\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly GetAllTasksHandler $getAllTasksHandler,
        private readonly GetMyTasksHandler $getMyTasksHandler,
        private readonly GetAllUsersHandler $getAllUsersHandler,
        private readonly CreateTaskHandler $createTaskHandler,
        private readonly CompleteTaskHandler $completeTaskHandler,
        private readonly DeleteTaskHandler $deleteTaskHandler,
    ) {
    }

    #[Route('/', name: 'task_index', methods: ['GET'])]
    public function index(): Response
    {
        // Admin sees all tasks, regular users see only their own
        if ($this->isGranted('ROLE_ADMIN')) {
            $query = new GetAllTasksQuery();
            $tasks = $this->getAllTasksHandler->handle($query);
        } else {
            $query = new GetMyTasksQuery();
            $tasks = $this->getMyTasksHandler->handle($query);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/create', name: 'task_create', methods: ['GET'])]
    public function create(): Response
    {
        $users = [];

        // Admin can assign task to any user during creation
        if ($this->isGranted('ROLE_ADMIN')) {
            $usersQuery = new GetAllUsersQuery();
            $users = $this->getAllUsersHandler->handle($usersQuery);
        }

        return $this->render('task/create.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'task_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $title = $request->request->get('title', '');
        $description = $request->request->get('description', '');
        $assigneeId = $request->request->get('assigneeId');

        $command = new CreateTaskCommand(
            title: $title,
            description: $description,
            assigneeId: $assigneeId !== '' ? $assigneeId : null
        );

        try {
            $this->createTaskHandler->handle($command);
            $this->addFlash('success', 'Task created successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('task_create');
        }

        return $this->redirectToRoute('task_index');
    }

    #[Route('/{id}/complete', name: 'task_complete', methods: ['POST'])]
    public function complete(string $id): Response
    {
        $command = new CompleteTaskCommand(taskId: $id);

        try {
            $this->completeTaskHandler->handle($command);
            $this->addFlash('success', 'Task completed!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('task_index');
    }

    #[Route('/{id}/delete', name: 'task_delete', methods: ['POST'])]
    public function delete(string $id): Response
    {
        $command = new DeleteTaskCommand(taskId: $id);

        try {
            $this->deleteTaskHandler->handle($command);
            $this->addFlash('success', 'Task deleted!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('task_index');
    }
}

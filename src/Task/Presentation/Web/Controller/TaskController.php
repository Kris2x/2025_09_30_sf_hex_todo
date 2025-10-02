<?php

namespace App\Task\Presentation\Web\Controller;

use App\Task\Application\Command\AssignTask\AssignTaskCommand;
use App\Task\Application\Command\AssignTask\AssignTaskHandler;
use App\Task\Application\Command\CompleteTask\CompleteTaskCommand;
use App\Task\Application\Command\CompleteTask\CompleteTaskHandler;
use App\Task\Application\Command\CreateTask\CreateTaskCommand;
use App\Task\Application\Command\CreateTask\CreateTaskHandler;
use App\Task\Application\Command\DeleteTask\DeleteTaskCommand;
use App\Task\Application\Command\DeleteTask\DeleteTaskHandler;
use App\Task\Application\Query\GetAllTasks\GetAllTasksHandler;
use App\Task\Application\Query\GetAllTasks\GetAllTasksQuery;
use App\User\Application\Query\GetAllUsers\GetAllUsersHandler;
use App\User\Application\Query\GetAllUsers\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly GetAllTasksHandler $getAllTasksHandler,
        private readonly GetAllUsersHandler $getAllUsersHandler,
        private readonly CreateTaskHandler $createTaskHandler,
        private readonly CompleteTaskHandler $completeTaskHandler,
        private readonly DeleteTaskHandler $deleteTaskHandler,
        private readonly AssignTaskHandler $assignTaskHandler,
    ) {
    }

    #[Route('/', name: 'task_index', methods: ['GET'])]
    public function index(): Response
    {
        $query = new GetAllTasksQuery();
        $tasks = $this->getAllTasksHandler->handle($query);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/create', name: 'task_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('task/create.html.twig');
    }

    #[Route('/create', name: 'task_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $title = $request->request->get('title', '');
        $description = $request->request->get('description', '');

        $command = new CreateTaskCommand(
            title: $title,
            description: $description
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

    #[Route('/{id}/assign', name: 'task_assign', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function assign(string $id): Response
    {
        $usersQuery = new GetAllUsersQuery();
        $users = $this->getAllUsersHandler->handle($usersQuery);

        return $this->render('task/assign.html.twig', [
            'taskId' => $id,
            'users' => $users,
        ]);
    }

    #[Route('/{id}/assign', name: 'task_assign_store', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function assignStore(string $id, Request $request): Response
    {
        $userId = $request->request->get('userId');

        $command = new AssignTaskCommand(
            taskId: $id,
            userId: $userId !== '' ? $userId : null
        );

        try {
            $this->assignTaskHandler->handle($command);
            $this->addFlash('success', $userId ? 'Task assigned successfully!' : 'Task unassigned!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('task_index');
    }
}

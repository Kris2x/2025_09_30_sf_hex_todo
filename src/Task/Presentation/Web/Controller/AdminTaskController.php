<?php

namespace App\Task\Presentation\Web\Controller;

use App\Task\Application\Command\AssignTask\AssignTaskCommand;
use App\Task\Application\Command\AssignTask\AssignTaskHandler;
use App\Task\Application\Command\UpdateTask\UpdateTaskCommand;
use App\Task\Application\Command\UpdateTask\UpdateTaskHandler;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Application\Query\UserQueryService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tasks')]
#[IsGranted('ROLE_ADMIN')]
final class AdminTaskController extends AbstractController
{
    public function __construct(
        private readonly UserQueryService $userQueryService,
        private readonly AssignTaskHandler $assignTaskHandler,
    )
    {
    }

    #[Route('/{id}/update', name: 'admin_task_update', methods: ['GET', 'POST'])]
    public function update(string $id, UpdateTaskHandler $updateTaskHandler, TaskRepositoryInterface $taskRepository, Request $request): Response
    {
        $users = $this->userQueryService->getAllUsers();
        $task = $taskRepository->findById($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $assigneeId = $request->request->get('assigneeId');

            try {
                $command = new UpdateTaskCommand(
                    taskId: $id,
                    title: $title,
                    description: $description,
                    assigneeId: $assigneeId !== '' ? $assigneeId : null
                );

                $updateTaskHandler->handle($command);
                $this->addFlash('success', 'Task updated successfully!');
                return $this->redirectToRoute('task_index');
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin/task/update.html.twig', [
            'task' => $task,
            'users' => $users,
        ]);

    }


    #[Route('/{id}/assign', name: 'admin_task_assign', methods: ['GET'])]
    public function assign(string $id): Response
    {
        $users = $this->userQueryService->getAllUsers();

        return $this->render('admin/task/assign.html.twig', [
            'taskId' => $id,
            'users' => $users,
        ]);
    }

    #[Route('/{id}/assign', name: 'admin_task_assign_store', methods: ['POST'])]
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
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('task_index');
    }
}

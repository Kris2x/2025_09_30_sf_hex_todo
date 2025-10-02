<?php

namespace App\Task\Presentation\Web\Controller;

use App\Task\Application\Command\AssignTask\AssignTaskCommand;
use App\Task\Application\Command\AssignTask\AssignTaskHandler;
use App\User\Application\Query\UserQueryService;
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
    ) {
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
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('task_index');
    }
}

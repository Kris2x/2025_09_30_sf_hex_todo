<?php

namespace App\User\Presentation\Web\Controller;

use App\User\Application\Command\DeleteUser\DeleteUserCommand;
use App\User\Application\Command\DeleteUser\DeleteUserHandler;
use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\User\Application\Command\RegisterUser\RegisterUserHandler;
use App\User\Application\Query\GetAllUsers\GetAllUsersHandler;
use App\User\Application\Query\GetAllUsers\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class AdminUserController extends AbstractController
{
    public function __construct(
        private readonly GetAllUsersHandler $getAllUsersHandler,
        private readonly RegisterUserHandler $registerUserHandler,
        private readonly DeleteUserHandler $deleteUserHandler,
    ) {
    }

    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(): Response
    {
        $query = new GetAllUsersQuery();
        $users = $this->getAllUsersHandler->handle($query);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'admin_user_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('admin/user/create.html.twig');
    }

    #[Route('/create', name: 'admin_user_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $email = $request->request->get('email', '');
        $firstName = $request->request->get('firstName', '');
        $lastName = $request->request->get('lastName', '');
        $password = $request->request->get('password', '');

        $command = new RegisterUserCommand(
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            password: $password
        );

        try {
            $this->registerUserHandler->handle($command);
            $this->addFlash('success', 'User created successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('admin_user_create');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{email}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(string $email): Response
    {
        $command = new DeleteUserCommand(email: $email);

        try {
            $this->deleteUserHandler->handle($command);
            $this->addFlash('success', 'User deleted!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_user_index');
    }
}

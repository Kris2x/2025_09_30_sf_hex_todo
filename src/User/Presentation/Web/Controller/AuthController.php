<?php

namespace App\User\Presentation\Web\Controller;

use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\User\Application\Command\RegisterUser\RegisterUserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly RegisterUserHandler $registerUserHandler
    ) {
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Jeśli user jest już zalogowany, przekieruj do strony głównej
        if ($this->getUser()) {
            return $this->redirectToRoute('task_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Ten endpoint jest obsługiwany przez Symfony Security
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function register(): Response
    {
        // Jeśli user jest już zalogowany, przekieruj do strony głównej
        if ($this->getUser()) {
            return $this->redirectToRoute('task_index');
        }

        return $this->render('auth/register.html.twig');
    }

    #[Route('/register', name: 'app_register_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $email = $request->request->get('email', '');
        $firstName = $request->request->get('firstName', '');
        $lastName = $request->request->get('lastName', '');
        $password = $request->request->get('password', '');

        try {
            $command = new RegisterUserCommand($email, $firstName, $lastName, $password);
            $this->registerUserHandler->handle($command);

            $this->addFlash('success', 'Account created successfully! Please login.');
            return $this->redirectToRoute('app_login');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Registration failed: ' . $e->getMessage());
            return $this->redirectToRoute('app_register');
        }
    }
}

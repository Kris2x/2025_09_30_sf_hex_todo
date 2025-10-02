<?php

namespace App\Common\Presentation\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        // Redirect authenticated users to tasks, guests to login
        if ($this->getUser()) {
            return $this->redirectToRoute('task_index');
        }

        return $this->redirectToRoute('app_login');
    }
}

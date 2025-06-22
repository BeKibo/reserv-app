<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        // If user is already logged in, redirect to appropriate page
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('admin_dashboard');
            }
            // For regular users, redirect to another appropriate page
            // For now, we'll just keep them on the login page
        }

        // Otherwise redirect to login page
        return $this->redirectToRoute('app_login');
    }
}

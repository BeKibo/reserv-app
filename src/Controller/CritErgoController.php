<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CritErgoController extends AbstractController
{
    #[Route('/crit/ergo', name: 'app_crit_ergo')]
    public function index(): Response
    {
        return $this->render('crit_ergo/index.html.twig', [
            'controller_name' => 'CritErgoController',
        ]);
    }
}

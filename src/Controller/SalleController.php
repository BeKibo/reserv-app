<?php

namespace App\Controller;

use App\Data\ReservationFilterData;
use App\Form\ReservationFilterType;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalleController extends AbstractController
{
    #[Route('/salle', name: 'app_salle')]
    public function index(Request $request, SalleRepository $salleRepository): Response
    {
        $filter = new ReservationFilterData();
        $form = $this->createForm(ReservationFilterType::class, $filter);
        $form->handleRequest($request);

        $salles = $salleRepository->findWithFilter($filter);
        $formView = $form->createView();

        return $this->render('salle/index.html.twig', [
            'form' => $formView,
            'salles' => $salles,
        ]);
    }
}

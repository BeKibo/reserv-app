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
    #[Route('/', name: 'app_salle')]
    public function index(Request $request, SalleRepository $salleRepository): Response
    {
        // Crée un objet contenant les données du filtre
        $filter = new ReservationFilterData();

        // Initialise le formulaire avec ces données
        $form = $this->createForm(ReservationFilterType::class, $filter);
        $form->handleRequest($request);

        // Récupère la valeur du champ non mappé "ville" manuellement
        $ville = $form->get('ville')->getData();
        if ($ville) {
            $filter->ville = $ville;
        }

        // Passe le filtre complet au repository
        $salles = $salleRepository->findWithFilter($filter);

        return $this->render('salle/index.html.twig', [
            'form' => $form->createView(),
            'salles' => $salles,
        ]);
    }
}

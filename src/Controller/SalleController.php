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

        // 🧪 Dump des données du filtre
        dump([
            'nom' => $filter->nom,
            'capaciteMin' => $filter->capaciteMin,
            'lieu' => $filter->lieu,
            'dateDebut' => $filter->dateDebut,
            'dateFin' => $filter->dateFin,
            'critergos' => $filter->critergos,
            'equipements' => $filter->equipements,
        ]);

        $salles = $salleRepository->findWithFilter($filter);

        //  On crée la vue du formulaire avant de manipuler ses champs
        $formView = $form->createView();

        // On accède aux enfants du champ "equipements" via FormView
        $equipementsView = $formView->children['equipements'];
        $grouped = [];

        foreach ($equipementsView as $child) {
            $label = $child->vars['label'] ?? 'Inconnu';
            preg_match('/^([A-Za-z]+)/', $label, $matches);
            $categorie = $matches[1] ?? 'Autres';
            $grouped[$categorie][] = $child;
        }

        return $this->render('salle/index.html.twig', [
            'salles' => $salles,
            'form' => $formView,
            'grouped_equipements' => $grouped,
        ]);
    }
}

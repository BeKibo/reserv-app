<?php

namespace App\Controller;

use DateType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'reservation_index')]
    public function index(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $reservations = $em->getRepository(Reservation::class)->findAll();

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salle = $reservation->getSalles();
            $dateDebut = $reservation->getDateDebut();
            $dateFin = $reservation->getDateFin();

            if ($salle->isReservedBetween($dateDebut, $dateFin)) {
                $this->addFlash('danger', 'La salle est déjà réservée à ces dates.');
            } else {
                $reservation->setUsers($security->getUser());
                $reservation->setValidation(false);
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Réservation soumise avec succès.');
                return $this->redirectToRoute('reservation_index');
            }
        }

        return $this->render('reservation/reservation.html.twig', [
            'reservations' => $reservations,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/salle/{id}', name: 'app_reservation_salle')]
    public function reservationSalle(
        int $id,
        SalleRepository $salleRepository,
        EntityManagerInterface $em,
        Security $security,
        Request $request
    ): Response {
        $salle = $salleRepository->find($id);

        if (!$salle) {
            throw new NotFoundHttpException('Salle introuvable.');
        }

        $reservation = new Reservation();
        $reservation->setSalles($salle);

        // Récupération des dates passées en query string et injection dans le formulaire
        $dateDebutParam = $request->query->get('dateDebut');
        $dateFinParam = $request->query->get('dateFin');

        if ($dateDebutParam) {
            $reservation->setDateDebut(new \DateType($dateDebutParam));
        }

        if ($dateFinParam) {
            $reservation->setDateFin(new DateType($dateFinParam));
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setUsers($security->getUser());
            $reservation->setValidation(false);

            $dateDebut = $reservation->getDateDebut();
            $dateFin = $reservation->getDateFin();

            if ($salle->isReservedBetween($dateDebut, $dateFin)) {
                $this->addFlash('danger', 'La salle est déjà réservée à ces dates.');
            } else {
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Réservation effectuée avec succès !');
                return $this->redirectToRoute('reservation_index');
            }
        }

        return $this->render('reservation/reservation.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }
}

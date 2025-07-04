<?php

namespace App\Controller;

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

use DateTimeImmutable;

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

        // Pré-remplissage des dates depuis la requête GET
        if ($request->query->get('dateDebut')) {
            $reservation->setDateDebut(new DateTimeImmutable($request->query->get('dateDebut')));
        }

        if ($request->query->get('dateFin')) {
            $reservation->setDateFin(new DateTimeImmutable($request->query->get('dateFin')));
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

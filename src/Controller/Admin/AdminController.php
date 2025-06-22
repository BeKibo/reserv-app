<?php

namespace App\Controller\Admin;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
abstract class AdminController extends AbstractController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Render a template with common admin variables
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function renderAdmin(string $view, array $parameters = [], Response $response = null): Response
    {
        // Add notifications to all admin templates
        $parameters['notifications'] = $this->notificationService->getAdminNotifications();
        $parameters['pending_reservations_count'] = $this->notificationService->getPendingReservationsCount();

        return $this->render($view, $parameters, $response);
    }
}

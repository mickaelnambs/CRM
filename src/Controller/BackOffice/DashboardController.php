<?php

namespace App\Controller\BackOffice;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(StatsService $statsService): Response
    {
        return $this->render('back_office/dashboard/index.html.twig', [
            "stats" => $statsService->getStats(),
        ]);
    }
}

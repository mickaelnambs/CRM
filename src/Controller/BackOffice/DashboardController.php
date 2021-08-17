<?php

namespace App\Controller\BackOffice;

use App\Controller\AbstractBaseController;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractBaseController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('back_office/dashboard/index.html.twig', [
            "users" => $this->em->getRepository(User::class)->getUsersCount(),
            "customers" => $this->em->getRepository(Customer::class)->getCustomersCount(),
            "invoices" => $this->em->getRepository(Invoice::class)->getInvoicesCount()
        ]);
    }
}

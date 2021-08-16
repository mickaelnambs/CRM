<?php

namespace App\Controller\API;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceIncrementationController
{
    private EntityManagerInterface $em;

    /**
     * InvoiceIncrementationController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Invoice $data): Invoice
    {
        $data->setChrono($data->getChrono() + 1);
        $this->em->flush();

        return $data;
    }
}
<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StatsService.
 */
class StatsService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * StatsService constructeur.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getStats()
    {
        $users      = $this->getUsersCount();
        $customers  = $this->getCustomersCount();
        $invoices   = $this->getInvoicesCount();

        return compact('users', 'customers', 'invoices');
    }

    public function getUsersCount()
    {
        return $this->em->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getCustomersCount()
    {
        return $this->em->createQuery('SELECT COUNT(c) FROM App\Entity\Customer c')->getSingleScalarResult();
    }

    public function getInvoicesCount()
    {
        return $this->em->createQuery('SELECT COUNT(i) FROM App\Entity\Invoice i')->getSingleScalarResult();
    }
}
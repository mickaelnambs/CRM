<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AbstractBaseController
 * @package App\Controller
 */
abstract class AbstractBaseController extends AbstractController
{
    /** @var EntityManagerInterface  */
    protected $em;

    /** @var UserPasswordEncoderInterface  */
    protected $passwordEncoder;

   /** @var DataTableFactory */
    private $dataTableFactory;

    /**
     * AbstractBaseController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param DataTableFactory $dataTableFactory
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, DataTableFactory $dataTableFactory)
    {
        $this->em               = $em;
        $this->passwordEncoder  = $passwordEncoder;
        $this->dataTableFactory = $dataTableFactory;
    }

    /**
     * @param $object
     * @return bool
     */
    public function save($object): bool
    {
        try {
            if (!$object->getId()) {
                $this->em->persist($object);
            }
            $this->em->flush();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $object
     * @return bool
     */
    public function remove($object): bool
    {
        try {
            if ($object) {
                $this->em->remove($object);
            }
            $this->em->flush();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $dataTableType
     * @param array $options
     * @return DataTable
     */
    public function createDataTable($dataTableType, array $options = []): DataTable
    {
        return $this->dataTableFactory->createFromType($dataTableType, $options);
    }
}

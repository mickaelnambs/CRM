<?php

namespace App\Form\DataTable;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use App\Controller\AbstractBaseController;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;

/**
 * Class UserDataTableType
 * @package App\DataTable
 */
class UserDataTableType extends AbstractBaseController implements DataTableTypeInterface
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable
            ->add("id", TextColumn::class, [
                "label" => "#"
            ])
            ->add("email", TextColumn::class, [
                "label" => "Adresse email"
            ])
            ->add("firstName", TextColumn::class, [
                "label" => "Prénom(s)"
            ])
            ->add("lastName", TextColumn::class, [
                "label" => "Nom"
            ])
            ->add("buttons", TextColumn::class, [
                "label" => "Actions",
                "orderable" => false,
                "searchable" => false,
                "className" => "button",
                "render" => function($value, $user) {
                    return $this->renderView("back_office/user/_button.html.twig", [
                        "user" => $user
                    ]);
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                "entity" => User::class,
                "query" => function (QueryBuilder $builder) {
                    $builder
                        ->from(User::class, "u")
                        ->select("u")
                        ->andWhere("u.id <> :current_user")
                        ->setParameter("current_user", $this->getUser()->getId())
                    ;
                },
            ])
        ;
    }
}
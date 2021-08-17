<?php


namespace App\Form\DataTable;


use App\Controller\AbstractBaseController;
use App\Entity\Customer;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;

/**
 * Class CustomerDataTableType
 * @package App\DataTable
 */
class CustomerDataTableType extends AbstractBaseController implements DataTableTypeInterface
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
            ->add("company", TextColumn::class, [
                "label" => "Entreprise"
            ])
            ->add("user", TextColumn::class, [
                "field" => "u.firstName",
                "label" => "Utilisateur"
            ])
            ->add("buttons", TextColumn::class, [
                "label" => "Actions",
                "orderable" => false,
                "searchable" => false,
                "className" => "button",
                "render" => function($value, $customer) {
                    return $this->renderView("back_office/customer/_button.html.twig", [
                        "customer" => $customer
                    ]);
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'hydrate' => Query::HYDRATE_ARRAY,
                "entity" => Customer::class,
                "query" => function (QueryBuilder $builder) {
                    $builder
                        ->distinct()
                        ->select("c")
                        ->addSelect("u")
                        ->from(Customer::class, "c")
                        ->join("c.user", "u")
                    ;
                },
            ])
        ;
    }
}
<?php


namespace App\DataTable;


use App\Controller\AbstractBaseController;
use App\Entity\Invoice;
use App\Entity\User;
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
class InvoiceDataTableType extends AbstractBaseController implements DataTableTypeInterface
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable
            ->add("id", TextColumn::class, [
                "label" => "#"
            ])
            ->add("amount", TextColumn::class, [
                "label" => "Prix en €"
            ])
            ->add("status", TextColumn::class, [
                "label" => "Status"
            ])
            ->add("customer", TextColumn::class, [
                "field" => "c.firstName",
                "label" => "Client"
            ])
            ->add("buttons", TextColumn::class, [
                "label" => "Actions",
                "orderable" => false,
                "searchable" => false,
                "className" => "button",
                "render" => function($value, $invoice) {
                    return $this->renderView("back_office/invoice/_button.html.twig", [
                        "invoice" => $invoice
                    ]);
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'hydrate' => Query::HYDRATE_ARRAY,
                "entity" => Invoice::class,
                "query" => function (QueryBuilder $builder) {
                    $builder
                        ->distinct()
                        ->select("i")
                        ->addSelect("c")
                        ->from(Invoice::class, "i")
                        ->join("i.customer", "c")
                    ;
                },
            ])
        ;
    }
}
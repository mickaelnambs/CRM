<?php


namespace App\Controller\BackOffice;

use App\Constant\MessageConstant;
use App\Controller\AbstractBaseController;
use App\DataTable\CustomerDataTableType;
use App\DataTable\InvoiceDataTableType;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Form\CustomerType;
use App\Form\InvoiceType;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InvoiceController
 * @package App\Controller\BackOffice
 *
 * @Route("/admin/invoices")
 */
class InvoiceController extends AbstractBaseController
{
    /**
     *
     * @Route("/", name="admin_invoice_index", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $table = $this->createDataTable(InvoiceDataTableType::class);
        $table->handleRequest($request);

        if ($table->isCallback()) return $table->getResponse();

        return $this->render("back_office/invoice/index.html.twig", [
            'invoices' => $table,
        ]);
    }

    /**
     * @Route("/manage/{id?}", name="admin_invoice_manage", methods={"GET", "POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Customer $invoice
     * @return Response
     */
    public function manage(Request $request, Invoice $invoice = null): Response
    {
        $invoice = $invoice ?? new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->save($invoice)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_invoice_index");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("admin_invoice_manage", ["id" => $invoice->getId() ?? null]);
        }
        return $this->render("back_office/invoice/manage.html.twig", [
            "form" => $form->createView(),
            "invoice" => $invoice
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_invoice_delete")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Customer $invoice
     * @return Response
     */
    public function delete(Customer $invoice): Response
    {
        if ($this->remove($invoice)) {
            $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
        } else {
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
        }
        return $this->redirectToRoute("admin_invoice_index");
    }
}
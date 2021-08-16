<?php


namespace App\Controller\BackOffice;

use App\Constant\MessageConstant;
use App\Controller\AbstractBaseController;
use App\DataTable\CustomerDataTableType;
use App\Entity\Customer;
use App\Form\CustomerType;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 * @package App\Controller\BackOffice
 *
 * @Route("/admin/customers")
 */
class CustomerController extends AbstractBaseController
{
    /**
     *
     * @Route("/", name="admin_customer_index", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $table = $this->createDataTable(CustomerDataTableType::class);
        $table->handleRequest($request);

        if ($table->isCallback()) return $table->getResponse();

        return $this->render("back_office/customer/index.html.twig", [
            'customers' => $table,
        ]);
    }

    /**
     * @Route("/manage/{id?}", name="admin_customer_manage", methods={"GET", "POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function manage(Request $request, Customer $customer = null): Response
    {
        $customer = $customer ?? new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->save($customer)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_customer_index");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("admin_customer_manage", ["id" => $customer->getId() ?? null]);
        }
        return $this->render("back_office/customer/manage.html.twig", [
            "form" => $form->createView(),
            "Customer" => $customer
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_customer_delete")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Customer $customer
     * @return Response
     */
    public function delete(Customer $customer): Response
    {
        if ($this->remove($customer)) {
            $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
        } else {
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
        }
        return $this->redirectToRoute("admin_customer_index");
    }
}
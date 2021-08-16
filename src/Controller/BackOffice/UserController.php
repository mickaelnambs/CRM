<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfileUpdate;
use App\Form\PasswordUpdateType;
use App\Constant\MessageConstant;
use App\Repository\UserRepository;
use App\DataTable\UserDataTableType;
use App\Controller\AbstractBaseController;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller\BackOffice
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractBaseController
{
    /**
     *
     * @Route("/", name="admin_user_index", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $table = $this->createDataTable(UserDataTableType::class);
        $table->handleRequest($request);

        if ($table->isCallback()) return $table->getResponse();

        return $this->render("back_office/user/index.html.twig", [
            'users' => $table,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            if ($this->save($user)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_user_index");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("admin_user_new");
        }
        return $this->render("back_office/user/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_user_update", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function update(Request $request, User $user): Response
    {
        $form = $this->createForm(ProfileUpdate::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->save($user)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_user_index");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("admin_user_update", ["id" => $user->getId()]);
        }
        return $this->render("back_office/user/update.html.twig", [
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * @Route("/profile/update", name="admin_update_profile", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateProfileAdmin(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUpdate::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->save($user)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_user_index");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("admin_update_profile", ["id" => $user->getId()]);
        }
        return $this->render("back_office/user/update.html.twig", [
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_user_delete")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user
     * @return Response
     */
    public function delete(User $user): Response
    {
        if ($this->remove($user)) {
            $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
        } else {
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
        }
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/password/update", name="password_update", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     */
    public function updatePassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->passwordEncoder->encodePassword($user, $form->get("newPassword")->getData());
            $user->setPassword($hash);

            if ($this->save($user)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUCCESS_MESSAGE);
                return $this->redirectToRoute("admin_app_logout");
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            return $this->redirectToRoute("password_update");
        }
        return $this->render("back_office/user/password_update.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/export/users", name="export_user")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param UserRepository $userRepository
     * @return StreamedResponse
     */
    public function exportUserToCsv(UserRepository $userRepository): StreamedResponse
    {
        $users = $userRepository->findAll();

        $streamedResponse = new StreamedResponse();
        $streamedResponse->setCallback(function() use ($users) {
            $handle = fopen('php://output', 'r+');
            $tableHead = ["#", "Nom", "Prénom", "Email"];
            fputcsv($handle, $tableHead, ';', '"');

            foreach($users as $user) {
                $data = [
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getEmail()
                ];
                fputcsv($handle, $data, ';', '"');
            }
            fclose($handle);
        }
        );
        $streamedResponse->headers->set('Content-Type', 'application/force-download');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="user.csv"');

        return $streamedResponse;
    }
}

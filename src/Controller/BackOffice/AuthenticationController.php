<?php

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * AuthenticationController.
 */
class AuthenticationController extends AbstractController
{
    /**
     * @Route("/login", name="admin_app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render("back_office/account/login.html.twig", [
            "username"  => $authenticationUtils->getLastUsername(),
            "hasError" => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="admin_app_logout")
     *
     * @return void
     */
    public function logout()
    {
        // Empty.
    }
}

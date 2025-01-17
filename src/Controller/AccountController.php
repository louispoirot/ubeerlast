<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function modifyPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = $this->getUser();
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $newPasswordConfirm = $request->request->get('new_password_confirm');

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect');
            } elseif ($newPassword !== $newPasswordConfirm) {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $this->getDoctrine()->getManager()->flush();
                
                $this->addFlash('success', 'Votre mot de passe a été modifié avec succès');
                return $this->redirectToRoute('app_account');
            }
        }

        return $this->render('account/password.html.twig');
    }
}
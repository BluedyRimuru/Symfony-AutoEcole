<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\FirstConnexionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function general(): Response
    {
        return $this->redirectToRoute('app_user_accueil');
    }

    #[Route('/user/accueil', name: 'app_user_accueil')]
    public function index(UserRepository $user, Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $user->findOneBy(["email" => $userid]);

        if($data->isVerified() == 0) {
            $this->addFlash("danger", "Veuillez changer le mot de passe !");
        }
        $roles = implode($data->getRoles()) . " ";
        $this->addFlash("primary", "Vos rôles sont les suivants : " . $roles);

        return $this->render('user/accueil.html.twig', [
            'controller_name' => 'UserController',
            'users' => $user->getUserById($data->getUserIdentifier()),
            'role' => $roles
        ]);
    }

    #[Route('/check', name: 'app_user_firstconnexion')]
    public function firstconnexion(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $usergeneral = new User();
//        $user = $this->getUser();
        $userid = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(["email" => $userid]);
        $form = $this->createForm(FirstConnexionType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($usergeneral, $password));
            $user->setIsVerified(1);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès !');
        } else if ($form['password']->get('first')->getData() != $form['password']->get('second')->getData()) {
            $this->addFlash('danger', "Les champs de mot de passe doivent correspondre.");
            $form->clearErrors(true);
        }  else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', "Le formulaire n'est pas valide !");
            $form->clearErrors(true);
        }

        if($user->isVerified() == 0) {
            return $this->render('firstconnexion/firstconnexion.html.twig', [
                'FirstConnexionForm' => $form->createView(),
            ]);
        } else {
            switch ($user->getRoles()[0]) {
                case "ROLE_ADMIN":
                    return $this->redirectToRoute('app_admin_accueil');
                case "ROLE_MONITEUR":
                case "ROLE_USER":
                    return $this->redirectToRoute('app_user_accueil');
            }
            $this->addFlash('danger', 'Votre rôle est invalide, contactez un administrateur.');
        }

    }
}
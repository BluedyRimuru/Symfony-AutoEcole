<?php

namespace App\Controller\Moniteur;

use App\Entity\User;
use App\Form\FirstConnexionType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/moniteur/profil', name: 'app_moniteur_profil')]
    public function index(UserRepository $user, Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $user->findOneBy(["email" => $userid]);
        $form = $this->createForm(ProfileType::class, $data);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $user->save($data, true);

            $this->addFlash('success','Modifié avec succès !');
            return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $error = $form->get('email')->getErrors();
            $form->clearErrors(true);
        }

        return $this->render('moniteur/profile/profile.html.twig', [
            'controller_name' => 'UserController',
            'users' => $user->getUserById($data->getUserIdentifier()),
            'ProfileType' => $form->createView(),
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    #[Route('/moniteur/changepassword', name: 'app_user_password')]
    public function changepassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $usergeneral = new User();
        $useremail = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(["email" => $useremail]);
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

        return $this->render('firstconnexion/firstconnexion.html.twig', [
            'FirstConnexionForm' => $form->createView(),
        ]);
    }
}
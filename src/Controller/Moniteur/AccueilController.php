<?php

namespace App\Controller\Moniteur;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/moniteur', name:'app_moniteur')]
    public function general(): Response
    {
        return $this->redirectToRoute('app_moniteur_accueil');
    }

    #[Route('/moniteur/accueil', name: 'app_moniteur_accueil')]
    public function index(UserRepository $user, Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $user->findOneBy(["email" => $userid]);

        if($data->isVerified() == 0) {
            $this->addFlash("danger", "Veuillez changer le mot de passe !");
        }
        $roles = implode($data->getRoles()) . " ";
        $this->addFlash("primary", "Vos rôles sont les suivants : " . $roles);

        return $this->render('moniteur/accueil.html.twig', [
            'controller_name' => 'MoniteurAccueil',
            'users' => $user->getUserById($data->getUserIdentifier()),
            'role' => $roles
        ]);
    }
}
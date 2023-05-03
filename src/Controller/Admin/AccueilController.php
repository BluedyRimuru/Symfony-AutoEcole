<?php

namespace App\Controller\Admin;

use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/admin', name: 'app_admin'), Security("is_granted('ROLE_ADMIN')")]
    public function general(): Response
    {
        return $this->redirectToRoute('app_admin_accueil');
    }

    #[Route('/admin/accueil', name: 'app_admin_accueil'), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $user, LeconRepository $lecons,Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $user->findOneBy(["email" => $userid]);

        // Données diverses
        $nbEleve = $user->getNombreDeEleve();
        $totalVentes = $lecons->getPrixAllLecon();
        $nbLecons = intval($lecons->getNombreDeLecons());

        // Commandes récentes
        $lastorder = $lecons->getLastOrders();

        $roles = implode($data->getRoles()) . " ";
        $this->addFlash("primary", "Vos rôles sont les suivants : " . $roles);

        return $this->render('admin/accueil.html.twig', [
            'controller_name' => 'Admin',
            'users' => $user->getUserById($data->getUserIdentifier()),
            'role' => $roles,
            'nbEleve' => $nbEleve,
            'totalVentes' => $totalVentes,
            'nbLecons' => $nbLecons,
            'lastOrder' => $lastorder,
            'admin' => $data,
        ]);
    }

    #[Route('/admin/theme', name: 'app_admin_theme'), Security("is_granted('ROLE_ADMIN')")]
    public function theme(UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        if ($user->isTheme()) { // True equals Dark
            $user->setTheme(False); // False equals Light
        } else {
            $user->setTheme(True); // True equals Dark
        }
        $userRepository->save($user, true);
//        $this->addFlash("success", "Votre thème a été changé avec succès !");
        return $this->redirectToRoute('app_admin_accueil');
    }

    // Ne pas utiliser & lire les lignes ci-dessous (v1 changement de thème)

    #[Route('/admin/light', name: 'app_admin_light'), Security("is_granted('ROLE_ADMIN')")]
    public function light(UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $user->setTheme(False); // False equals Light
        $userRepository->save($user, true);
//        $this->addFlash("success", "Votre thème a été changé avec succès !");
        return $this->redirectToRoute('app_admin_accueil');
    }

    #[Route('/admin/dark', name: 'app_admin_dark'), Security("is_granted('ROLE_ADMIN')")]
    public function dark(UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $user->setTheme(True); // False equals Dark
        $userRepository->save($user, true);
//        $this->addFlash("success", "Votre thème a été changé avec succès !");
        return $this->redirectToRoute('app_admin_accueil');
    }
}
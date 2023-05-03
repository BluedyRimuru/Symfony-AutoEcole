<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController extends AbstractController
{
    #[Route('/admin/analytics', name: 'app_admin_analytics'), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository, VehiculeRepository $vehiculeRepository, LeconRepository $leconRepository): Response
    {
        // Les données
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        // On envoie tout dans la vue
        return $this->render('admin/analytics/index.html.twig', [
            'controller_name' => 'AnalyticsController',
            'vehicules' => $vehiculeRepository->getVehiculesLesPlusUtilises(),
            'moniteurs' => $leconRepository->getMoniteurLesPlusSollicites(),
            'admin' => $data,
        ]);
    }
}

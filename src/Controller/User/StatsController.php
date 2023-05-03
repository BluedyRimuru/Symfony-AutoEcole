<?php

namespace App\Controller\User;

use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/user/stats', name: 'app_user_stats', methods: ['GET', 'POST'])]
    public function index(CategorieRepository $categorieRepository, UserRepository $user): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $user->findOneBy(["email" => $userid])->getId();
        $prixPermis = $categorieRepository->getPrixTotalDuPermis($data);
        $prixAPayer = $categorieRepository->getPrixRestantAPayer($data);

        return $this->render('user/stats.html.twig', [
            'prixPermis' => $prixPermis,
            'prixAPayer' => $prixAPayer,
//            'data' => 11,
//            'informations' => 'Cette page est actuellement en construction, repassez plus tard !'
        ]);
    }
}
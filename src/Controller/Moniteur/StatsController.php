<?php

namespace App\Controller\Moniteur;

use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/moniteur/stats', name: 'app_moniteur_stats')]
    public function index(CategorieRepository $categorieRepository, UserRepository $user): Response
    {
        $thisuser = $this->getUser()->getUserIdentifier();
        $id = $user->findOneBy(['email' => $thisuser]);
        $curr_year = date('Y');
        $curr_month = date('m');

        echo $categorieRepository->getCAByDay($id, date('Y-m-d'));

        $totalPrice = $categorieRepository->getCATotalUser($id);
        $semainePrice = $categorieRepository->getCASemaineMoniteur($id, $curr_year);

        return $this->render('moniteur/stats.html.twig', [
            'TotalPrice' => $totalPrice,
            'SemainePrice' => $semainePrice,
        ]);
    }
}
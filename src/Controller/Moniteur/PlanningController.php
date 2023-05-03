<?php

namespace App\Controller\Moniteur;

use App\Repository\LeconRepository;
use App\Repository\LicenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/moniteur/planning', name: 'app_moniteur_planning')]
    public function index(LeconRepository $leconRepository): Response
    {
        $rdvs = [];

        foreach ($leconRepository->getPlanning($this->getUser()->getId()) as $lecon) {
            $start = $lecon->getDate()->format('Y-m-d') . " " . $lecon->getHeure()->format('H:i:s');
            $newtimestamp = strtotime($lecon->getDate()->format('Y-m-d') . " " . $lecon->getHeure()->format('H:i') . '+ 2 hours');

            foreach ($lecon->getEquipe() as $item) {

                if ($item->getId() != $this->getUser()->getId()){
                    $eleve = $item->getNom() . ' ' . $item->getPrenom();
                }

            }

            $rdvs[] = [
                'id' => $lecon->getId(),
                'start' => $start,
                'end' => date('Y-m-d H:i:s',$newtimestamp),
                'title' => "Lecon : {$lecon->getId()}",
                'description'=> "
                    Eleve : {$eleve}
                    Immatriculation : {$lecon->getCodevehicule()->getImmatriculation()} 
                    Categorie : {$lecon->getCodevehicule()->getCodeCategorie()->getLibelle()}",
                'background_color' => '#E7BA2E',
                'border_color' => '#000000',
                'text_color' => '#FFFFFF',
                'allday' => false,
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('moniteur/planning/index.html.twig', compact('data'));
    }
}
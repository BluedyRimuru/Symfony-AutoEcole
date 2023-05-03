<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\NewMoniteurType;
use App\Form\User1Type;
use App\Repository\CategorieRepository;
use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoniteurController extends AbstractController
{
    #[Route('/admin/moniteur', name: 'app_admin_moniteur'), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        return $this->render('admin/moniteur/index.html.twig', [
            'moniteurs' => $userRepository->findByRole("ROLE_MONITEUR"),
            'admin' => $data,
        ]);
    }

    #[Route('/admin/moniteur/planning/{id}', name: 'app_admin_moniteur_planning', methods: ['GET']), Security("is_granted('ROLE_ADMIN')")]
    public function planning(User $user, LeconRepository $leconRepository, UserRepository $userRepository): Response
    {

        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $rdvs = [];

        foreach ($leconRepository->getPlanning($user->getId()) as $lecon) {
            $start = $lecon->getDate()->format('Y-m-d')." ".$lecon->getHeure()->format('H:i:s');
            $newtimestamp = strtotime($lecon->getDate()->format('Y-m-d')." ".$lecon->getHeure()->format('H:i'). '+ 2 hours');

            foreach ($lecon->getEquipe() as $item ){
                if ($item->getId() != $user->getId()){
                    $nom = $item->getNom() . ' ' . $item->getPrenom();
                }
            }

            $rdvs[] = [
                'id' => $lecon->getId(),
                'start' => $start,
                'end' => date('Y-m-d H:i:s',$newtimestamp),
                'title' => "Lecon : {$lecon->getId()}",
                'description'=> "
                    Eleve : {$nom}
                    Immatriculation : {$lecon->getCodevehicule()->getImmatriculation()}
                    Categorie : {$lecon->getCodevehicule()->getCodeCategorie()->getLibelle()}",
                'background_color' => '#E7BA2E',
                'border_color' => '#000000',
                'text_color' => '#FFFFFF',
                'allday' => false,
            ];
        }

        $datapl = json_encode($rdvs);

        return $this->render(
            'admin/moniteur/planning.html.twig',
            [
                'admin' => $data,
                'data' => $datapl,
            ],
        );
    }

    #[Route('/admin/moniteur/{id}', name: 'app_admin_moniteur_show', methods: ['GET']), Security("is_granted('ROLE_ADMIN')")]
    public function show(User $user, UserRepository $userRepository, CategorieRepository $categorieRepository, LeconRepository $leconRepository): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $CAMoniteur = $categorieRepository->getCATotalUser($user);
        $LeconsMoniteur = $leconRepository->getLeconInfo($user->getId());
        $NombreDeLeconMoniteur = $leconRepository->getNombreDeLeconsParUser($user->getId());
        $resteAPayer = $categorieRepository->getPrixRestantAPayer($user->getId());

        return $this->render('admin/moniteur/show.html.twig', [
            'user' => $user,
            'admin' => $data,
            'caMoniteur' => $CAMoniteur,
            'leconsMoniteur' => $LeconsMoniteur,
            'nbLecons' => $NombreDeLeconMoniteur,
            'resteAPayer' => $resteAPayer
        ]);
    }

    #[Route('/admin/moniteurs/ajout', name: 'app_admin_moniteur_add', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function news(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(NewMoniteurType::class, $user);
        $form->handleRequest($request);

        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_MONITEUR"]);

            $userRepository->save($user, true);

            $this->addFlash('success', 'Le moniteur a été enregistré avec succès !');
            return $this->redirectToRoute('app_admin_moniteur_add', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/moniteur/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'admin' => $data,
        ]);
    }
}
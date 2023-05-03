<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\NewEleveType;
use App\Repository\CategorieRepository;
use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EleveController extends AbstractController
{
    #[Route('/admin/eleve', name: 'app_admin_eleve'), Security("is_granted('ROLE_ADMIN')")]
    public function index()
    {
        return $this->redirectToRoute('app_admin_eleve_id',["id" => 1]);
    }

    #[Route('/admin/eleve/ajout', name: 'app_admin_eleve_add', methods: ['GET', 'POST'])]
    public function news(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(NewEleveType::class, $user);
        $form->handleRequest($request);

        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_USER"]);

            $userRepository->save($user, true);

            $this->addFlash('success', "L'élève a été enregistré avec succès !");
            return $this->redirectToRoute('app_admin_eleve_add', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/eleve/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'admin' => $data,
        ]);
    }

    #[Route('/admin/eleve/show/{id}', name: 'app_admin_user_show', methods: ['GET']), Security("is_granted('ROLE_ADMIN')")]
    public function show(User $user, UserRepository $userRepository, CategorieRepository $categorieRepository, LeconRepository $leconRepository): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $caEleve = $categorieRepository->getCATotalUser($user);
        $leconEleve = $leconRepository->getLeconInfo($user->getId());
        $nombreDeLecon = $leconRepository->getNombreDeLeconsParUser($user->getId());
        $resteAPayer = $categorieRepository->getPrixRestantAPayer($user->getId());
        return $this->render('admin/eleve/show.html.twig', [
            'user' => $user,
            'admin' => $data,
            'caEleve' => $caEleve,
            'leconsEleve' => $leconEleve,
            'nbLecons' => $nombreDeLecon,
            'resteAPayer' => $resteAPayer
        ]);
    }

    // Numéro de page
    #[Route('/admin/eleve/{id}', name: 'app_admin_eleve_id'), Security("is_granted('ROLE_ADMIN')")]
    public function page(int $id, UserRepository $userRepository): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        // Code à corriger dans le futur (Objectif : Bloquer les page supérieure n'ayants pas d'élèves
        if ($id < 1) {
            return $this->redirectToRoute('app_admin_eleve_id', ['id' => 1]);
        } else {
            $valueUser = $userRepository->getEleveByPage($id);
            if (empty($valueUser)) {
                return $this->redirectToRoute('app_admin_eleve_id', ['id' => 1]);
            } else {
                return $this->render('admin/eleve/index.html.twig', [
                    'page_actuelle' => $id,
                    'eleves' => $valueUser,
                    'admin' => $data,
                ]);
            }
        }
    }

    #[Route('/admin/eleve/planning/{id}', name: 'app_admin_eleve_planning', methods: ['GET']), Security("is_granted('ROLE_ADMIN')")]
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
                    Moniteur : {$nom}
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
            'admin/eleve/planning.html.twig',
            [
                'admin' => $data,
                'data' => $datapl,
            ],
        );
    }
}
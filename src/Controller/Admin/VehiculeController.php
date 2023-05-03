<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Vehicule;
use App\Form\CategorieType;
use App\Form\VehiculeType;
use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    #[Route('/admin/vehicule', name: 'app_admin_vehicule', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository, VehiculeRepository $vehiculeRepository, Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        $vehiculeUse = $vehiculeRepository->getVehiculeLePlusUtilise();

        if ($form->isSubmitted() && $form->isValid()) {
            $vehiculeRepository->save($vehicule, true);
            $this->addFlash('success', 'Véhicule ajouté avec succès !');
            return $this->redirectToRoute('app_admin_vehicule', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
            'admin' => $data,
            'form' => $form,
            'VehiculeUitlise' => $vehiculeUse
        ]);
    }
    #[Route('/admin/vehicule/delete/{idVehi}', name: 'app_admin_delete_vehicule', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function deleteVehicule(LeconRepository $leconRepository, VehiculeRepository $vehiculeRepository,Request $request, int $idVehi, ): Response
    {

        $delVehi = $vehiculeRepository->findOneBy(['id' => $idVehi]);

        if ($leconRepository->findOneBy(['codevehicule'=> $idVehi]) == null) {
                $vehiculeRepository->remove($delVehi, true);
        } else {

            $this->addFlash('danger', 'Le véhicule est lié a une leçon.');
            return $this->redirectToRoute('app_admin_vehicule', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_admin_vehicule');
    }

    #[Route('/admin/vehicule/update/{idVehi}', name: 'app_admin_update_vehicule', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function updateVehicule(UserRepository $userRepository, VehiculeRepository $vehiculeRepository, Request $request, int $idVehi, EntityManagerInterface $entityManager): Response
    {

        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $vehicule = $vehiculeRepository->findOneBy(['id' => $idVehi]);

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $vehicule->setImmatriculation($form['immatriculation']->getData());
            $vehicule->setMarque($form['marque']->getData());
            $vehicule->setAnnee($form['annee']->getData());
            $vehicule->setCodecategorie($form['codecategorie']->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_vehicule');
        }

        return $this->renderForm('admin/vehicule/update.html.twig', [
            'vehicule' => $vehiculeRepository->findOneBy(['id'=>$idVehi]),
            'form' => $form,
            'admin' => $data
        ]);
    }
}
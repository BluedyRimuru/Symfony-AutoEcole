<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\LicenceRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/admin/categorie', name: 'app_admin_categorie', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository, CategorieRepository $categorieRepository, Request $request): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        $locomotion = $categorieRepository->getLocomotionLaPlusUtilise();

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);
            $this->addFlash('success', 'Catégorie ajoutée avec succès !');
            return $this->redirectToRoute('app_admin_categorie', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'form' => $form,
            'locomotion' => $locomotion,
            'admin' => $data,
        ]);
    }

    #[Route('/admin/categorie/delete/{idCat}', name: 'app_admin_delete_categorie', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function deleteCategorie(CategorieRepository $categorieRepository, LicenceRepository $licenceRepository, VehiculeRepository $vehiculeRepository,Request $request, int $idCat, ): Response
    {

        $delCat = $categorieRepository->findOneBy(['id' => $idCat]);

        if ($licenceRepository->findOneBy(['codecategorie'=> $idCat]) == null) {
            if ($vehiculeRepository->findOneBy(['codecategorie'=>$idCat]) == null) {
                $categorieRepository->remove($delCat, true);
            } else {
                $this->addFlash('danger', 'La catégorie est liée a un véhicule.');
                return $this->redirectToRoute('app_admin_categorie', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            $this->addFlash('danger', 'La catégorie est liée a une licence.');
            return $this->redirectToRoute('app_admin_categorie', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_admin_categorie');
    }

    #[Route('/admin/categorie/update/{idCat}', name: 'app_admin_update_categorie', methods: ['GET', 'POST']), Security("is_granted('ROLE_ADMIN')")]
    public function updateCategorie(UserRepository $userRepository, CategorieRepository $categorieRepository, Request $request, int $idCat, EntityManagerInterface $entityManager): Response
    {


        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $categorie = $categorieRepository->findOneBy(['id' => $idCat]);

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $categorie->setLibelle($form['libelle']->getData());
            $categorie->setPrix($form['prix']->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_categorie');
        }

        return $this->renderForm('admin/categorie/update.html.twig', [
            'categorie' => $categorieRepository->findOneBy(['id'=>$idCat]),
            'form' => $form,
            'admin' => $data
        ]);
    }
}
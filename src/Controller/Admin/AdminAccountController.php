<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\User;
use App\Form\AdminAccountType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends AbstractController
{
    #[Route('/admin/account', name: 'app_admin_account'), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository, Request $request, Filesystem $filesystem, EntityManagerInterface $entityManager): Response
    {
        // On récupère le thème
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        // Création du formulaire
        $user = new User();
        $form = $this->createForm(AdminAccountType::class, $user);
        $form->handleRequest($request);

        // Condition du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $image = new Image();
                // Vérifie que le fichier est bien une image
                if (!in_array($imageFile->getMimeType(), ['image/jpg','image/jpeg', 'image/png', 'image/gif'])) {
                    $this->addFlash('danger', 'Le fichier doit être une image.');
                    return $this->redirectToRoute('app_admin_account');
                }

                // Vérifie que le fichier ne dépasse pas la taille maximale
                if ($imageFile->getSize() > 2000000) {
                    $this->addFlash('danger', 'Le fichier est trop volumineux (max : 2000Mo).');
                    return $this->redirectToRoute('app_admin_account');
                }

                // Génère un nom de fichier unique pour éviter les conflits
                $filename = uniqid() . '.' . $imageFile->guessExtension();

                // Enregistre l'image dans le dossier public/uploads/profiles/
                $imageFile->move(
                    $this->getParameter('profiles_directory'),
                    $filename
                );

                // On récupère le path de l'image actuelle
                $actualPath = $data->getImage()->getPath(); // = image/avatar_default.png

                // On vérifie que le path actuel n'est pas égal à celui du jeu de donnée sinon on supprime l'image
                if ($actualPath != 'image/avatar_default.png') {
                    $filesystem->remove($actualPath); // Suppression du lien
                }
                $entityManager->remove($entityManager->getRepository(Image::class)->find($data->getImage()->getId())); // Suppression dans la bdd

                // Enregistre le chemin d'accès à l'image dans la base de données
                $image->setPath('uploads/profiles/' . $filename);
                $data->setImage($image);
                $userRepository->save($data, true);
            }

            $this->addFlash('success', 'La modification de vos données a été effectuée avec succès !');
            return $this->redirectToRoute('app_admin_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/account/index.html.twig', [
            'admin' => $data,
            'form' => $form,
        ]);
    }
}

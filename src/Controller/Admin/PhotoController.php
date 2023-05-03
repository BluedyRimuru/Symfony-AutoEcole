<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\User;
use App\Form\PhotoType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    #[Route('/admin/photo', name: 'app_admin_photo'), Security("is_granted('ROLE_ADMIN')")]
    public function index(UserRepository $userRepository, Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $userid = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $userid]);

        $theme = $data->isTheme();

        $form = $this->createForm(PhotoType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Vérifie que le fichier est bien une image
                if (!in_array($imageFile->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                    $this->addFlash('error', 'Le fichier doit être une image.');
                    return $this->redirectToRoute('app_admin_photo');
                }

                // Vérifie que le fichier ne dépasse pas la taille maximale
                if ($imageFile->getSize() > 50000000) {
                    $this->addFlash('error', 'Le fichier est trop volumineux (max : 50000ko).');
                    return $this->redirectToRoute('app_admin_photo');
                }

                // Génère un nom de fichier unique pour éviter les conflits
                $filename = uniqid() . '.' . $imageFile->guessExtension();

                // Enregistre l'image dans le dossier public/uploads/profiles/
                $imageFile->move(
                    $this->getParameter('profiles_directory'),
                    $filename
                );

                // Récupère la route de l'ancienne photo
                $oldFileName = $this->getParameter('delete_photo_profiles').$data->getImage()->getPath();

                // Vérifie si le fichier existe
                if (file_exists($oldFileName)){
                    unlink($oldFileName); // Suppression du fichier
                    $deleteImg = $entityManager->getRepository(Image::class)->find($data->getImage()->getId());
                    $entityManager->remove($deleteImg);
                }

                // Enregistre le chemin d'accès à l'image dans la base de données
                $image = new Image();
                $image->setPath('uploads/profiles/' . $filename);
                $data->setImage($image);
                $userRepository->save($data, true);
            }
        }

        return $this->render('admin/photo/index.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }
}
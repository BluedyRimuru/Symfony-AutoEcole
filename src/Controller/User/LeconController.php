<?php

namespace App\Controller\User;

use App\Entity\Lecon;
use App\Form\ULeconType;
use App\Repository\LeconRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeconController extends AbstractController
{
    #[Route('/user/lecon', name: 'app_user_lecon_nouvelle', methods: ['GET', 'POST'])]
    public function nouvelle(Request $request, LeconRepository $leconRepository, UserRepository $userRepository): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser()->getUserIdentifier();
        $data = $userRepository->findOneBy(["email" => $user]);

        // Création du formulaire
        $lecon = new Lecon();
        $formLecon = $this->createForm(ULeconType::class, $lecon);
        $formLecon->handleRequest($request);

        /* A faire :
         * - Si l'utilisateur est bien vérifié sur le site
         * - Si l'utilisateur n'a pas de leçon à cette heure et date
         * - Si le moniteur n'a pas de leçon à cette heure et date
         * - Si le moniteur est qualifié à donner cette leçon (catégorie)
         * - Si le véhicule est disponible à cette heure et date
         * - Si le moniteur existe bien dans la base de donnée et possède le rôle moniteur
         */

        if($formLecon->isSubmitted() && $formLecon->isValid())
        {
            // On récupère les informations du formulaire :
            $formHeure = $formLecon->get('heure')->getData();
            $formDate = $formLecon->get('date')->getData();
            $formMoniteur = $formLecon->get('equipe')->getData();
            $formVehicule = $formLecon->get('codevehicule')->getData();

            // Préparation des variables :
            $formMoniteurID = $formMoniteur->getId();
            $formVehiculeID = $formVehicule->getId();

            if (!$data->isVerified()) // Si l'utilisateur n'est pas vérifié alors :
            {
                $this->addFlash('danger', 'Vous devez changer de mot de passe si vous voulez vous inscrire à une leçon !');
                return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
            }
            if (empty($userRepository->checkMoniteur($formMoniteurID))) // Si le moniteur n'existe dans la base de donnée ou n'est pas un moniteur alors :
            {
                $this->addFlash('danger', 'Le moniteur sélectionné n\'en est pas un !');
                return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
            }
            if (!empty($leconRepository->getLeconEleveForEleve($user, $formDate, $formHeure))) // Si l'utilisateur n'a pas de leçons à cette heure et date alors :
            {
                $this->addFlash('danger', 'Vous possédez déjà une leçon à cette heure !');
                return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
            }
            if (!empty($leconRepository->getLeconEleveForMoniteur($formMoniteurID, $formDate, $formHeure))) // Si le moniteur n'a pas de leçon à cette heure et date alors :
            {
                $this->addFlash('danger', 'Ce moniteur possède déjà une leçon à cette heure !');
                return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
            }
            if (!empty($leconRepository->getLeconEleveForVehicule($formVehiculeID, $formDate, $formHeure))) // Si le véhicule est déjà utilisé pour cette heure et date alors :
            {
                $this->addFlash("danger", "Ce véhicule est déjà utilisé pour cette heure !");
                return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
            }
            // Message flash
            $this->addFlash("success", "Votre leçon a été validée avec succès ! Vous pouvez consulter vos leçons depuis votre planning");

            // Ajout des données dans la base
            $lecon->addEquipe($data);
            $lecon->addEquipe($formMoniteur);
            $lecon->setReglee(0); // La valeur par défaut est sur 0 pour signifier qu'elle n'est pas payée.
            $leconRepository->save($lecon, true);

            // Retour à la page
            return $this->redirectToRoute('app_user_lecon_nouvelle', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/lecon/new-lecon.html.twig', [
            'lecon' => $lecon,
            'form' => $formLecon,
        ]);

    }
}
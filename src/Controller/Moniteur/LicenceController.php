<?php

namespace App\Controller\Moniteur;

use App\Entity\Licence;
use App\Form\MoniteurLicenceType;
use App\Repository\LicenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LicenceController extends AbstractController
{
    #[Route('/moniteur/licence', name: 'app_moniteur_licence')]
    public function index(Request $request, LicenceRepository $licenceRepository): Response
    {
        $licence = new Licence();
        $moniteur = $this->getUser();

        $formLicence = $this->createForm( MoniteurLicenceType::class, $licence);
        $formLicence->handleRequest($request);

        if ($formLicence->isSubmitted() && $formLicence->isValid()) {

            $date = new \DateTime('now');
            $codecategorie = $formLicence->get('codecategorie')->getData();

            if(!empty($licenceRepository->getLicenceByMoniteur($moniteur, $codecategorie))) {
                $this->addFlash("danger", "Vous possédez déjà cette licence.");
            } else {

                $licence->setDateobtention($date);
                $licence->setCodecategorie($codecategorie);
                $licence->setObtention($moniteur);

                $licenceRepository->save($licence, true);

                $this->addFlash("success", "Votre licence a été ajoutée avec succès !");
                return $this->redirectToRoute('app_moniteur_licence', [], Response::HTTP_SEE_OTHER);

            }
        }

        return $this->renderForm('moniteur/licence/licence.html.twig', [
            'licence' => $licence,
            'form' => $formLicence,
        ]);
    }
}
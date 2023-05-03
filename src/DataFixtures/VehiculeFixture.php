<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VehiculeFixture extends Fixture
{

    private $counter = 1;

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $listVehicule = [
            0 => ["immatriculation" => "123 AB 21", "marque" => "Mercedes", "modele" => "Spania", "annee" => 2000, "codecategorie" => 1],
            1 => ["immatriculation" => "234 BC 21", "marque" => "Peugeot", "modele" => "Sisancys", "annee" => 1996, "codecategorie" => 1],
            2 => ["immatriculation" => "345 CD 21", "marque" => "Renault", "modele" => "Morgane", "annee" => 1995, "codecategorie" => 1],
            3 => ["immatriculation" => "456 DE 21", "marque" => "Peugeot", "modele" => "Catsansys", "annee" => 1999, "codecategorie" => 1],
            4 => ["immatriculation" => "567 EF 21", "marque" => "Kawasaki", "modele" => "Zephyr", "annee" => 1997, "codecategorie" => 1],
            5 => ["immatriculation" => "678 FG 21", "marque" => "Renault", "modele" => "Betton", "annee" => 1999, "codecategorie" => 1],
            6 => ["immatriculation" => "789 GH 21", "marque" => "Iveco", "modele" => "Roader", "annee" => 1998, "codecategorie" => 1],
            7 => ["immatriculation" => "890 HJ 21", "marque" => "Oceansea", "modele" => "Tempest", "annee" => 1999, "codecategorie" => 1],
        ];

        foreach ($listVehicule as $vehicule)
        {
            $vehic = new \App\Entity\Vehicule();
            $categorie = $this->getReference('cat-'.rand(1, 5));
            $vehic->setImmatriculation($vehicule["immatriculation"])->setMarque($vehicule["marque"])->setModele($vehicule["modele"])->setAnnee($vehicule["annee"]);
            $vehic->setCodecategorie($categorie);
            $this->addReference('vehic-'.$this->counter, $vehic);
            $this->counter++;
            $manager->persist($vehic);
        }

        $manager->flush();
    }
}
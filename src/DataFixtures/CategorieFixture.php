<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixture extends Fixture
{

    private $counter = 1;

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $listCat = [
            0 => ["libelle" => "Bus", "prix" => 1.40],
            1 => ["libelle" => "Trotinette", "prix" => 2],
            2 => ["libelle" => "Velo", "prix" => 0.50],
            3 => ["libelle" => "Voiture", "prix" => 2.12],
            4 => ["libelle" => "Train", "prix" => 1.99],
        ];

        foreach ($listCat as $categorie)
        {
            $cat = new \App\Entity\Categorie();
            $cat->setLibelle($categorie["libelle"])->setPrix($categorie["prix"]);
            $manager->persist($cat);

            $this->addReference('cat-'.$this->counter, $cat);
            $this->counter++;
        }

        $manager->flush();
    }
}

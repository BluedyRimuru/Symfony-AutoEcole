<?php

namespace App\DataFixtures;

use App\Entity\Licence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LicenceFixture extends Fixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++){
            $licence = new Licence();
            $licence->setCodecategorie($this->getReference('cat-'.rand(1,5)));
            $licence->setDateobtention($faker->dateTime('now'));
            $licence->setObtention($this->getReference('moniteur-'.$i));
            $manager->persist($licence);
        }
        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }
}
<?php

namespace App\DataFixtures;

use App\Entity\Images;
use App\Entity\Marques;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MarquesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($marque=1; $marque<=10 ; $marque++){
            $marques = new Marques();
            $marques->setName($faker->name());
            $image = $this->getReference('image-'.rand(1, 10));
            $this->addReference('marque-'.$marque, $marques);
            $marques->setImage($image);

            $manager->persist($marques);
        }
        
        

        $manager->flush();
    }
    public function getDependencies(){
        return [
            ImagesFixtures::class
        ];
    }


}

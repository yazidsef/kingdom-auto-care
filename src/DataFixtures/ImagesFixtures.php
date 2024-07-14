<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker= Factory::create('fr_FR');
        for($img=1 ; $img<=10 ; $img++){
            $image = new Images();
            $image->setName($faker->imageUrl());
            $this->addReference('image-'.$img,$image);
            $manager->persist($image);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            ProductsFixtures::class
        ];
    }
}

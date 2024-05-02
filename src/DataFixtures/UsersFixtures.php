<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UsersFixtures extends Fixture
{ 
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder, 
        private SluggerInterface $slugger 
      ){}
    public function load(ObjectManager $manager): void
    {
      $faker= Factory::create('fr_FR');
      for($usr=1 ; $usr <= 5 ; $usr++){
          $admin = new Users ();
          $admin->setEmail($faker->email());
          $admin->setLastname($faker->lastName());
          $admin->setFirstname($faker->firstName());
          $admin->setAddress($faker->streetAddress());
          $admin->setZipcode($faker->postcode());
          $admin->setCity($faker->city());
          $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin,'admin')
          );
          $admin->setRoles(['ROLE_ADMIN']);
          $manager->persist($admin);
      }

      
      $manager->flush();
    }
}

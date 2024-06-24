<?php

namespace App\DataFixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $compound = $this->CreateProducts('compound', 10, 'compound', 100, $manager);

        $manager->flush();
    }
    public function __construct(private SluggerInterface $slugger){}
  
    public function CreateProducts(string $name,int $stock, string $description , float $price , ObjectManager $manager)
    {
        $faker= Factory::create('fr_FR');
        for($prod=1; $prod<=30;$prod++){
            $product = new Products();
            $product->setName($faker->name());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setDescription($faker->text());
            $product->setPrix($faker->numberBetween(1,10));
            $product->setMarques($this->getReference('marque-'. $faker->numberBetween(1,5)));
            $product->setStock($faker->numberBetween(1,10));
            $product->setMarques($this->getReference('marque-'. $faker->numberBetween(1,5)));
            $product->setCategories($this->getReference('informatique'));
            $this->addReference('prod-'.$prod,$product);
            $manager->persist($product);
        }
            $manager->flush();
    }
    public function getDependencies()
    {
        return [
            CategoriesFixtures::class,
            MarquesFixtures::class
        ];
    }
    
}

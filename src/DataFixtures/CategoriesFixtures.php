<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;
    public function __construct(private SluggerInterface $slugger) {}
    public function load(ObjectManager $manager): void
    {
        $cuisine = $this->createCategory('Cuisine',null,$manager);
        $table= $this->createCategory(name:'Table',parent:$cuisine,manager:$manager);
        $chaise=$this->createCategory(name:'Chaise',parent:$cuisine,manager:$manager);
        $potagier= $this->createCategory(name:'potagier',parent:$cuisine,manager:$manager);
        $assiettes = $this->createCategory('assiettes',$table,$manager);
        $informatique=$this->createCategory('informatique',null,$manager);
        $dell=$this->createCategory('dell',$informatique,$manager);
        $ipad=$this->createCategory('ipad',$informatique,$manager);
        $iphone=$this->createCategory('iphone',$informatique,$manager);
        $manager->flush();
    }
    public function createCategory(string $name , Categories $parent = null , ObjectManager $manager , int $order = 1)
    {
        $category = new Categories();
        $category->setName(($name));
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $this->addReference($category->getName(),$category);
        $category->setParent($parent);
        $category->setCategoryOrder($order);
        $manager->persist($category);

        return $category;
    }
    
}

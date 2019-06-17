<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
  const CAT = ['php', 'java', 'go', 'javascript', 'Ruby', 'DevOps', 'python'];
  
  public function load(ObjectManager $manager)
  {
/*
    foreach(self::CAT as $k => $v){

      $category = new Category();
      $category->setName($v);
      $manager->persist($category);
      $this->addReference('categorie_' . $k, $category);
    }
    $manager->flush();*/
  }

  public static function getCat(){
    return self::CAT;
  }
}
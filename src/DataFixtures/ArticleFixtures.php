<?php
namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Services\Slugify;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

  public function load(ObjectManager $manager)
  {
    $faker = Faker\Factory::create('fr_FR');
    $nbRefs = count(CategoryFixtures::CAT) - 1;
    $s = new Slugify();
    
    for($i=0; $i < 1000; $i++){
      $article = new Article();
      $article->setTitle(mb_strtolower($faker->company));
      $article->setContent(mb_strtolower($faker->realText($maxNbChars = 200, $indexSize = 2)));
      $article->setCategory($this->getReference('categorie_'.rand(0, $nbRefs)));
      $article->setSlug($s->generate($article->getTitle()));    
      $manager->persist($article);
    }
    $manager->flush();
  }

  public function getDependencies()
  {
      return [CategoryFixtures::class];
  }
}
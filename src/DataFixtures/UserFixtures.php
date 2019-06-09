<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $cypher;

    public function __construct(UserPasswordEncoderInterface $cypher)
    {
        $this->cypher = $cypher;   
    }

    public function load(ObjectManager $manager)
    {
        $author = new User();
        $author->setEmail('author@monsite.com');
        $author->setRoles(['ROLE_AUTHOR']);
        $author->setPassword($this->cypher->encodePassword(
            $author,
            'authorpassword'
        ));

        $manager->persist($author);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->cypher->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();

    }
}

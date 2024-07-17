<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;

class AuthorFixtures extends Fixture 
{
    public const AUTHOR_REFERENCE = 'author_';
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setFirstName($faker->firstName());
            $author->setLastName($faker->lastName());
            $manager->persist($author);
            $this->addReference(self::AUTHOR_REFERENCE . $i, $author);
        }
        $manager->flush();
    }
}
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;

class BookFixtures extends Fixture 
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $book = new Book();
            $book->setTitle('Titre :' . $i);
            $book->setAuthor('Auteur :' . $i);
            $book->setCoverText('Résumé :' . $i);
            $manager->persist($book);
        }
        $manager->flush();
    }
}
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;
use App\DataFixtures\AuthorFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $listAuthors = [];
        for ($i = 0; $i < 10; $i++) {
            $listAuthors[] = $this->getReference(AuthorFixtures::AUTHOR_REFERENCE . $i);
        }

        for ($i = 0; $i < 10; $i++) {
            $book = new Book();
            $book->setTitle('Titre :' . $i);
            $book->setCoverText('Résumé :' . $i);
            /*Link between a book and a random author from the listAuthor array
            No need to add a getReference to the following line, because it already
            exists at line 18*/
            $book->setAuthor($listAuthors[array_rand($listAuthors)]);
            $manager->persist($book);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AuthorFixtures::class,
        ];
    }
}
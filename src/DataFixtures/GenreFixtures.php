<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        {
            for ($i=1; $i<=5; $i++) {
                $genre = new Genre();
                $genre->setName("Genre $i");
                $genre->setDescription("This is detail info about genre $i");
                $manager->persist($genre);
                $manager->flush();
            }
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        for ($i = 0; $i < 10; $i++) {

            $ingredient = new Ingredient();
            $ingredient->setNom('assa' . $i);
            $ingredient->setPrix($faker->randomFloat(1, 1, 200));
            $ingredient->setCreatAt(new DateTimeImmutable());
            $manager->persist($ingredient);
            $this->addReference('INGREDIENT' . $i , $ingredient);
        }

        $manager->flush();
    }
}
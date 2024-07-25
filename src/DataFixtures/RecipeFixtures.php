<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->faker = Factory::create();
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($this->faker));

        for ($i = 0; $i < 8; $i++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->unique->foodName());
            $recipe->setSlug($this->slugger->slug($recipe->getName())->lower());
            $recipe->setTime($this->faker->numberBetween(10, 300));
            $recipe->setNbPersonne($this->faker->numberBetween(1, 50));
            $recipe->setDifficulty($this->faker->numberBetween(1, 6));
            $recipe->setDescription($this->faker->realText(200, 2));
            $recipe->setPrice($this->faker->randomFloat(2, 0, 100));
            $recipe->setIsFavorite($this->faker->boolean());

            for ($j = 0; $j < mt_rand(1, 6); $j++) {
                $recipe->addIngredient($this->getReference('INGREDIENT' . mt_rand(0, 9)));
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [IngredientFixtures::class];
    }
}

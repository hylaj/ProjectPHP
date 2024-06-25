<?php
/**
 * Rating fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Rating;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class RatingFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class RatingFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(50, 'ratings', function (int $i) {
            $rating = new Rating();

            $rating->setBookRating($this->faker->numberBetween(1, 5));

            $user = $this->getRandomReference('users');
            $rating->setUser($user);

            $book = $this->getUniqueReference('books');
            $rating->setBook($book);

            return $rating;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class,
            BookFixtures::class, ];
    }// end getDependencies()
}

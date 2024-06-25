<?php
/**
 * Rental fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Rental;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class RentalFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class RentalFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
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

        $this->createMany(20, 'rentals', function (int $i) {
            $rental = new Rental();
            $rental->setComment($this->faker->unique()->sentence);
            $rental->setRentalDate(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            $rental->setStatus($this->faker->boolean());
            $owner = $this->getRandomReference('users');
            $rental->setOwner($owner);

            $book = $this->getUniqueReference('books');

            $rental->setBook($book);
            $book->setAvailable(false);

            return $rental;
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

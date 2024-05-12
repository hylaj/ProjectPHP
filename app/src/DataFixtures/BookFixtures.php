<?php
/**
 * Book fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Book;

/**
 * Class BookFixtures.
 */
class BookFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $book = new Book();
            $book->setTitle($this->faker->sentence);
            $book->setAuthor($this->faker->name);
            $book->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $this->manager->persist($book);
        }
        $this->manager->flush();
    }
}

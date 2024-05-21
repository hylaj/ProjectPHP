<?php
/**
 * Book fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class BookFixtures.
 */
class BookFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{


    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(
            100,
            'books',
            function (int $i) {
                $book = new Book();
                $book->setTitle($this->faker->sentence(2));
                $book->setAuthor($this->faker->name);
                $book->setCreatedAt(
                    \DateTimeImmutable::createFromMutable(
                        $this->faker->dateTimeBetween('-100 days', '-1 days')
                    )
                );
                $category = $this->getRandomReference('categories');
                $book->setCategory($category);

                for ($i = rand(1,7); $i < 5; $i++) {
                    $tag = $this->getRandomReference('tags');
                    $book->addTag($tag);
                }

                return $book;
            }
        );

        $this->manager->flush();

    }//end loadData()


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
        return [CategoryFixtures::class];

    }//end getDependencies()


}//end class

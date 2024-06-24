<?php
/**
 * Book fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class BookFixtures.
 */
class BookFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    private $covers = ['default0.jpg', 'default1.jpg', 'default2.jpg', 'default3.jpg', 'default4.jpg'];

    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(
            100,
            'books',
            function (int $i) {
                $book = new Book();
                $book->setTitle($this->faker->sentence(2));
                $book->setAuthor($this->faker->name);
                $book->setReleaseDate(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                );
                $category = $this->getRandomReference('categories');
                $book->setCategory($category);

                $tags = $this->getRandomReferences(
                    'tags',
                    $this->faker->numberBetween(0, 5)
                );
                foreach ($tags as $tag) {
                    $book->addTag($tag);
                }
                $book->setAvailable(true);
                $book->setDescription($this->faker->text(1000));

                $randomIndex = array_rand($this->covers);
                $coverFilename = $this->covers[$randomIndex];

                $book->setCoverFilename($coverFilename);

                return $book;
            }
        );

        $this->manager->flush();
    }// end loadData()

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class, 1: TagFixtures::class, 2: UserFixtures::class}
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
            UserFixtures::class,
        ];
    }// end getDependencies()
}// end class

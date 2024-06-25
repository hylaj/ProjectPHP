<?php
/**
 *App Fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
/**
 *Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * Load.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $manager->flush();
    }// end load()
}// end class

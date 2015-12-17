<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 15.12.15
 * Time: 14:25
 */

namespace AppBackEndBundle\DataFixtures\ORM;

use AppBackEndBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Faker\Factory;

/**
 * Class LoadCategories
 *
 * @codeCoverageIgnore
 *
 * @package AppBackEndBundle\DataFixtures\ORM
 */
class LoadUserCategories extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $commonUser = $this->getReference('common_user');
        $secondaryUser = $this->getReference('secondary_user');

        $category = new Category();
        $category->setName('Test category');
        $category->addUser($commonUser);

        $manager->persist($category);

        // My categories.
        for ($i = 0; $i < 9; $i++) {
            $category = new Category();
            $category->setName($faker->sentence());
            $category->addUser($commonUser);

            $manager->persist($category);
        }

        // Another user categories.
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->sentence());
            $category->addUser($secondaryUser);

            $manager->persist($category);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 15;
    }
}
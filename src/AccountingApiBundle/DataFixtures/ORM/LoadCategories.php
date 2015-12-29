<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 15.12.15
 * Time: 14:25
 */

namespace AccountingApiBundle\DataFixtures\ORM;

use AccountingApiBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class LoadCategories
 *
 * @codeCoverageIgnore
 *
 * @package AccountingApiBundle\DataFixtures\ORM
 */
class LoadCategories extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Global category 1');
        $category->setGlobal();
        $manager->persist($category);
        $this->addReference('global_category', $category);

        $category = new Category();
        $category->setName('Global category 2');
        $category->setGlobal();
        $manager->persist($category);

        $category = new Category();
        $category->setName('Global category 3');
        $category->setGlobal();
        $manager->persist($category);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
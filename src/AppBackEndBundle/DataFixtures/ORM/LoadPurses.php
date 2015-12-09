<?php

namespace AppBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Proxies\__CG__\AppBackEndBundle\Entity\Purse;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Faker\Factory;

/**
 * Class LoadUserData
 *
 * Used for creating fake data for user entity.
 *
 * @package AppBackEndBundle\DataFixtures\ORM
 */
class LoadPurses extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $testUser = $this->getReference('test-user');

        for ($i = 0; $i < 3; $i++) {
            $purse = new Purse();
            $purse->setName($faker->sentence(4));
            $purse->setBalance($faker->randomFloat(2, 0, 100000));
            $purse->setUser($testUser);

            $manager->persist($purse);
        }

        $purse = new Purse();
        $purse->setName('Exist purse');
        $purse->setBalance($faker->randomFloat(2, 0, 100000));
        $purse->setUser($testUser);

        $manager->persist($purse);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}

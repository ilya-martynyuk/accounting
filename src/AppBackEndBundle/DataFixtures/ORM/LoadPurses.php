<?php

namespace AppBackEndBundle\DataFixtures\ORM;

use AppBackEndBundle\Entity\Purse;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Faker\Factory;

/**
 * Class LoadUserData
 *
 * Used for creating fake data for user entity.
 *
 * @package AppBackEndBundle\DataFixtures\ORM
 */
class LoadPurses extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $testUser = $this->getReference('test_user');

        $purse = new Purse();
        $purse->setName('Exist purse');
        $purse->setBalance($faker->randomFloat(2, 0, 100000));
        $purse->setUser($testUser);
        $this->addReference('test_purse_1', $purse);

        $manager->persist($purse);

        $purse = new Purse();
        $purse->setName('Purse to be patched');
        $purse->setBalance(123.123);
        $purse->setUser($testUser);
        $this->addReference('test_purse_2', $purse);

        $manager->persist($purse);

        for ($i = 0; $i < 3; $i++) {
            $purse = new Purse();
            $purse->setName($faker->sentence(4));
            $purse->setBalance($faker->randomFloat(2, 0, 100000));
            $purse->setUser($testUser);

            $manager->persist($purse);
        }

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

<?php

namespace AccountingApiBundle\DataFixtures\ORM;

use AccountingApiBundle\Entity\Purse;
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
 * @codeCoverageIgnore
 *
 * @package AccountingApiBundle\DataFixtures\ORM
 */
class LoadPurses extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $commonUser = $this->getReference('common_user');
        $secondaryUser = $this->getReference('secondary_user');

        $purse = new Purse();
        $purse->setName('Common user purse');
        $purse->setBalance(150.00);
        $purse->setUser($commonUser);
        $this->addReference('common_user_purse', $purse);

        $manager->persist($purse);

        for ($i = 0; $i < 3; $i++) {
            $purse = new Purse();
            $purse->setName($faker->sentence(4));
            $purse->setBalance($faker->randomFloat(2, 0, 100000));
            $purse->setUser($commonUser);

            $manager->persist($purse);
        }

        $purse = new Purse();
        $purse->setName('Secondary user purse');
        $purse->setBalance(100.00);
        $purse->setUser($secondaryUser);
        $this->addReference('secondary_user_purse', $purse);

        $manager->persist($purse);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }
}

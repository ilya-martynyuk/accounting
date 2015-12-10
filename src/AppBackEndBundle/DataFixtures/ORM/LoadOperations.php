<?php

namespace AppBackEndBundle\DataFixtures\ORM;

use AppBackEndBundle\Entity\Operation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBackEndBundle\Entity\Purse;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Faker\Factory;

/**
 * Class LoadOperations
 *
 * @package AppBackEndBundle\DataFixtures\ORM
 */
class LoadOperations extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $testPurse1 = $this->getReference('test_purse_1');

        for ($i = 0; $i < 100; $i++) {
            $operation = new Operation();
            $operation->setDirection($faker->randomElement(['-', '+']));
            $operation->setAmount($faker->randomFloat($faker->randomFloat(2, 0, 1000)));
            $operation->setDescription($faker->text(300));
            $operation->setDate($faker->dateTime());
            $operation->setPurse($testPurse1);

            $manager->persist($operation);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}

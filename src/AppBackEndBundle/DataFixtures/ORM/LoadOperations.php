<?php

namespace AppBackEndBundle\DataFixtures\ORM;

use AppBackEndBundle\Entity\Operation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Faker\Factory;

/**
 * Class LoadOperations
 *
 * @codeCoverageIgnore
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
        $commonUserPurse = $this->getReference('common_user_purse');

        $operation = new Operation();
        $operation->setDirection($faker->randomElement(['-', '+']));
        $operation->setAmount(777.66);
        $operation->setDescription('Common user purse operation');
        $operation->setDate($faker->dateTime());
        $operation->setPurse($commonUserPurse);
        $manager->persist($operation);

        $operation = new Operation();
        $operation->setDirection('+');
        $operation->setAmount(12.54);
        $operation->setDescription('Increase operation');
        $operation->setDate($faker->dateTime());
        $operation->setPurse($commonUserPurse);
        $manager->persist($operation);

        $operation = new Operation();
        $operation->setDirection('-');
        $operation->setAmount(43.00);
        $operation->setDescription('Decrease operation');
        $operation->setDate($faker->dateTime());
        $operation->setPurse($commonUserPurse);
        $manager->persist($operation);

        for ($i = 0; $i < 100; $i++) {
            $operation = new Operation();
            $operation->setDirection($faker->randomElement(['-', '+']));
            $operation->setAmount($faker->randomFloat($faker->randomFloat(2, 0, 1000)));
            $operation->setDescription($faker->text(300));
            $operation->setDate($faker->dateTime());
            $operation->setPurse($commonUserPurse);
            $manager->persist($operation);
        }

        $secondaryUserPurse = $this->getReference('secondary_user_purse');

        for ($i = 0; $i < 50; $i++) {
            $operation = new Operation();
            $operation->setDirection($faker->randomElement(['-', '+']));
            $operation->setAmount($faker->randomFloat($faker->randomFloat(2, 0, 1000)));
            $operation->setDescription($faker->text(300));
            $operation->setDate($faker->dateTime());
            $operation->setPurse($secondaryUserPurse);
            $manager->persist($operation);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 30;
    }
}

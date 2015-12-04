<?php

namespace AppBackEndBundle\DataFixtures\ORM;

use AppBackEndBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
class LoadUserData implements FixtureInterface, ContainerAwareInterface
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
     * Loads fake users.
     *
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $encoder = $this->container->get('security.password_encoder');

        // Common user which will be used as authenticated.
        $user = new User();
        $user->setName('test');
        $user->setEmail('test@test.com');

        $password = $encoder->encodePassword($user, 'test');
        $user->setPassword($password);

        $manager->persist($user);

        // Other users.
        for ($i = 0; $i < 10000; $i++) {
            $user = new User();
            $user->setName($faker->unique()->name);
            $user->setEmail($faker->unique()->email);

            $password = $encoder->encodePassword($user, $faker->password);
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

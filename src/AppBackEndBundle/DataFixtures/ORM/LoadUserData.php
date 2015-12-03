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
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $encoder = $this->container->get('security.password_encoder');

        // This user is used for authentication for tests.
        $user = new User();
        $user->setName('test');
        $user->setEmail('test@gmail.com');

        $password = $encoder->encodePassword($user, 'test');
        $user->setPassword($password);

        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setEmail($faker->email);

            $password = $encoder->encodePassword($user, $faker->password);
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

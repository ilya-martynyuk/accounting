<?php

namespace AccountingApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use AccountingApiBundle\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
class LoadUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $user->setUsername('common_user');
        $user->setEmail('common_user@test.com');

        $password = $encoder->encodePassword($user, 'test');
        $user->setPassword($password);
        $this->addReference('common_user', $user);

        $manager->persist($user);

        // Second user
        $user = new User();
        $user->setUsername('secondary_user');
        $user->setEmail('secondary_user@test.com');

        $password = $encoder->encodePassword($user, 'test');
        $user->setPassword($password);
        $this->addReference('secondary_user', $user);

        $manager->persist($user);

        // Other users.
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->unique()->name);
            $user->setEmail($faker->unique()->email);

            $password = $encoder->encodePassword($user, $faker->password);
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}

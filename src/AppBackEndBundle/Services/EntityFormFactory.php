<?php

namespace AppBackEndBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityFormFactory
 *
 * Used for creating of EntityForm objects.
 *
 * @package AppBackEndBundle\Services
 */
class EntityFormFactory
{
    /**
     * Returns EntityForm new instance
     *
     * @param ContainerInterface $container
     *
     * @return EntityForm
     */
    public function createEntityForm(ContainerInterface $container)
    {
        return new EntityForm($container);
    }
}

<?php

namespace AppBackEndBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OperationType
 *
 * @package AppBackEndBundle\Form
 */
class OperationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('direction')
            ->add('amount')
            ->add('description')
            ->add('date')
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['direction'])) {
                    $data['direction'] = '-';
                }

                $event->setData($data);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class' => 'AppBackEndBundle\Entity\Operation',
            'allow_extra_fields'  => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }
}

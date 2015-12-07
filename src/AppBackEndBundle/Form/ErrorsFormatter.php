<?php

namespace AppBackEndBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

/**
 * Class ErrorsFormatter
 *
 * Is used for formatting form errors in necesary format.
 *
 * @package AppBackEndBundle\Form
 */
class ErrorsFormatter
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns formatted form errors.
     *
     * @param Form $form
     * @return mixed
     */
    public function getErrors(Form $form)
    {
        if ($err = $this->getChildErrors($form)) {
            $errors["form"] = $err;
        }

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getChildErrors($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }

    /**
     * Returns child errors.
     *
     * @param Form $form
     * @return array
     */
    public function getChildErrors(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $message = $this
                ->container
                ->get('translator')
                ->trans($error->getMessage(), array());

            array_push($errors, $message);
        }

        return $errors;
    }
}

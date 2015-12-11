<?php

namespace AppBackEndBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

/**
 * Class FormErrorsFormatter
 *
 * Is used for formatting form errors in necesary format.
 *
 * @package AppBackEndBundle\Form
 */
class FormErrorsFormatter
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
        $formErrors = [];

        if ($err = $this->getFormErrors($form)) {
            $formErrors["form"] = $err;
        }

        foreach ($form->all() as $key => $child) {
            if ($error = $this->getFormErrors($child)) {
                $formErrors[$key] = $error;
            }
        }

        return $formErrors;
    }

    /**
     * Returns child errors.
     *
     * @param Form $form
     * @return array An array of errors
     */
    public function getFormErrors(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $message = $this
                ->container
                ->get('translator')
                ->trans($error->getMessage(), array());

            $errors[] = $message;
        }

        return $errors;
    }
}

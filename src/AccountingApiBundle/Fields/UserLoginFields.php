<?php

namespace AccountingApiBundle\Fields;

class UserLoginFields implements FieldsInterface
{
    public function getFields()
    {
        return [
            'username', 'password'
        ];
    }
}

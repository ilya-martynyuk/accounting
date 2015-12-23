<?php

namespace AccountingApiBundle\Fields;

class PurseFields implements FieldsInterface
{
    public function getFields()
    {
        return [
            'name', 'balance'
        ];
    }
}

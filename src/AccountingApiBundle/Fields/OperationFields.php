<?php

namespace AccountingApiBundle\Fields;

class OperationFields implements FieldsInterface
{
    public function getFields()
    {
        return [
            'direction', 'amount', 'description', 'date'
        ];
    }
}
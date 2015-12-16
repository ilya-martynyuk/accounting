<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 16.12.15
 * Time: 17:49
 */

namespace AppBackEndBundle\Entity;

trait PopulateEntity
{
    /**
     * Populates entity with given data.
     * Data should be represented as an array of key => value (field name => field value) parameters.
     *
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
}

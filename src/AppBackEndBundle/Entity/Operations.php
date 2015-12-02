<?php

namespace AppBackEndBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operations
 *
 * @ORM\Table(name="operations")
 * @ORM\Entity(repositoryClass="AppBackEndBundle\Repository\OperationsRepository")
 */
class Operations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="purse_id", type="integer")
     */
    private $purseId;

    /**
     * @var array
     *
     * @ORM\Column(name="direction", type="array")
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set purseId
     *
     * @param integer $purseId
     *
     * @return Operations
     */
    public function setPurseId($purseId)
    {
        $this->purseId = $purseId;

        return $this;
    }

    /**
     * Get purseId
     *
     * @return int
     */
    public function getPurseId()
    {
        return $this->purseId;
    }

    /**
     * Set direction
     *
     * @param array $direction
     *
     * @return Operations
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return array
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Operations
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Operations
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}


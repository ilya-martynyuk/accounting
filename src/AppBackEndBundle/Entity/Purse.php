<?php

namespace AppBackEndBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Purses
 *
 * @ORM\Table(name="purses")
 * @ORM\Entity(repositoryClass="AppBackEndBundle\Repository\PursesRepository")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Purse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     *
     * @JMS\Expose
     */
    private $balance;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="purses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @JMS\Expose
     */
    private $name;


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
     * Set balance
     *
     * @param string $balance
     *
     * @return Purses
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Purses
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
        return $this->user;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Purses
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Purses
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}


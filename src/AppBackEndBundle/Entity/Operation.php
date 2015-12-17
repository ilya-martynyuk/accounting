<?php

namespace AppBackEndBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Operations
 *
 * @codeCoverageIgnore
 *
 * @ORM\Table(name="operations")
 * @ORM\Entity(repositoryClass="AppBackEndBundle\Repository\OperationsRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Operation
{
    use TraitPopulateEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose()
     * @JMS\Groups({"details"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Purse",inversedBy="operations")
     * @ORM\JoinColumn(name="purse_id",referencedColumnName="id")
     */
    private $purse;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", length=1)
     *
     * @Assert\Choice(choices={"+", "-"})
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     *
     * @Assert\Range(min=1)
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     *
     * @Assert\Length(min=0, max=500)
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $date;


    public function __construct()
    {
        $this->direction = '-';
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set purse
     *
     * @param integer $purse
     *
     * @return Operations
     */
    public function setPurse($purse)
    {
        $this->purse = $purse;

        return $this;
    }

    /**
     * Get purse
     *
     * @return int
     */
    public function getPurse()
    {
        return $this->purse;
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
}

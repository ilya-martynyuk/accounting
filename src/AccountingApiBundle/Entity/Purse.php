<?php

namespace AccountingApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Purses
 *
 * @codeCoverageIgnore
 *
 * @ORM\Table(name="purses")
 * @ORM\Table(
 *      name="purses",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="purse_name_user_id_idx", columns={"name", "user_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="AccountingApiBundle\Repository\PursesRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(
 *      fields={"name", "user"},
 *      errorPath="name",
 *      message="Purse with the same name is already exist"
 * )
 *
 * @JMS\ExclusionPolicy("all")
 */
class Purse
{
    use TraitPopulateEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose()
     * @JMS\Groups({"details"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="balance",type="decimal",precision=10,scale=2, nullable=true)
     *
     * @Assert\Range(min=1)
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $balance;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="purses")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Operation",mappedBy="purse")
     */
    private $operations;

    /**
     * @var string
     *
     * @ORM\Column(name="name",type="string",length=255)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Purse name must be at least {{ limit }} characters long",
     *      maxMessage = "Purse name cannot be longer than {{ limit }} characters"
     * )
     *
     * @JMS\Expose()
     * @JMS\Groups({"create", "details"})
     */
    private $name;


    public function __construct()
    {
        $this->operations = new ArrayCollection();
        $this->balance = 0;
    }

    public function getOperations()
    {
        return $this->operations;
    }

    public function addOperation($operation)
    {
        $this->operations[] = $operation;

        return $this;
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
     * Get userId
     *
     * @return int
     */
    public function getUser()
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

    public function increaseBalance($amount)
    {
        $this->balance += $amount;

        return $this;
    }

    public function decreaseBalance($amount)
    {
        if ($this->balance - $amount < 0) {
            $this->balance = 0;
        } else {
            $this->balance -= $amount;
        }

        return $this;
    }

    public function processOperation(Operation $operation)
    {
        if ($operation->getDirection() === '-') {
            $this->decreaseBalance($operation->getAmount());
        } else {
            $this->increaseBalance($operation->getAmount());
        }
    }

    public function removeOperation(Operation $operation)
    {
        if ($operation->getDirection() === '+') {
            $this->decreaseBalance($operation->getAmount());
        } else {
            $this->increaseBalance($operation->getAmount());
        }
    }
}

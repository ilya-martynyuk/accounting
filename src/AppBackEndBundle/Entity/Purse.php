<?php

namespace AppBackEndBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Purses
 *
 * @ORM\Table(name="purses")
 * @ORM\Table(
 *      name="purses",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="name_user_id_idx", columns={"name", "user_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="AppBackEndBundle\Repository\PursesRepository")
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
    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="balance",type="decimal",precision=10,scale=2)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="float")
     *
     * @JMS\Expose
     */
    private $balance;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="purses")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     *
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @ORM\oneToMany(targetEntity="Operation",mappedBy="purse")
     */
    private $operations;

    /**
     * @var string
     *
     * @ORM\Column(name="name",type="string",length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Purse name must be at least {{ limit }} characters long",
     *      maxMessage = "Purse name cannot be longer than {{ limit }} characters"
     * )
     *
     * @JMS\Expose
     */
    private $name;


    public function __construct()
    {
        $this->operations = new ArrayCollection();
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
}


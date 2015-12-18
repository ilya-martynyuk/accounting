<?php

namespace AccountingApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Categories
 *
 * @codeCoverageIgnore
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="AccountingApiBundle\Repository\CategoriesRepository")
 *
 * @UniqueEntity(
 *      fields={"name"},
 *      message="Such category is already exist"
 * )
 *
 * @JMS\ExclusionPolicy("all")
 */
class Category
{
    use TraitPopulateEntity;

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Category name must be at least {{ limit }} characters long",
     *      maxMessage = "Category name cannot be longer than {{ limit }} characters"
     * )
     *
     * @JMS\Expose
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\ManyToMany(targetEntity="User")
     */
    private $users;

    /**
     * @var bool
     *
     * @ORM\Column(name="global", type="boolean", length=1)
     *
     * @JMS\Expose
     */
    private $global;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->global = false;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setGlobal($global = true)
    {
        $this->global = $global;

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
     * Set name
     *
     * @param string $name
     *
     * @return Categories
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
}


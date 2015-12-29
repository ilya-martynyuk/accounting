<?php

namespace AccountingApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Categories
 *
 * @codeCoverageIgnore
 *
 * @ORM\Table(
 *      name="categories",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="category_name_user_id_idx", columns={"name", "user_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="AccountingApiBundle\Repository\CategoriesRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(
 *      fields={"name", "user"},
 *      errorPath="name",
 *      message="Category with the same name is already exist"
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
     * @JMS\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Category name must be at least {{ limit }} characters long",
     *      maxMessage = "Category name cannot be longer than {{ limit }} characters"
     * )
     *
     * @JMS\Expose()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="categories")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="global", type="boolean", length=1)
     *
     * @JMS\Expose()
     */
    private $global;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->global = false;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
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

    public function isGlobal()
    {
        return $this->global;
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

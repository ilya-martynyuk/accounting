<?php

namespace AppBackEndBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @codeCoverageIgnore
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBackEndBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class User implements UserInterface, \Serializable
{
    use TraitPopulateEntity;

    const ROLE_USER = 'ROLE_USER';

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
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=50)
     *
     * @JMS\Expose
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email()
     *
     * @JMS\Expose
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="Purse", mappedBy="user")
     */
    private $purses;

    /**
     * @var int
     *
     * @ORM\ManyToMany(targetEntity="Category")
     */
    private $categories;


    /**
     * Initialize entity.
     */
    public function __construct()
    {
        $this->purses = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getPurses()
    {
        return $this->purses;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Attach new category to user.
     *
     * @param Category $category New purse object.
     * @return $this
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Add new purse to user.
     *
     * @param Purse $purse New purse object.
     * @return $this
     */
    public function addPurse(Purse $purse)
    {
        $this->purses[] = $purse;

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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return [
            self::ROLE_USER
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function checkDefaults()
    {
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }
}


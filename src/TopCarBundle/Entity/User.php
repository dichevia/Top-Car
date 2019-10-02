<?php

namespace TopCarBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="TopCarBundle\Repository\Users\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * errorPath="email",
 * message="It appears you have already registered with this email."
 *)
 *
 */
class User implements UserInterface
{
    const DEFAULT_AVATAR = 'default-avatar.jpg';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Email can't be blank.")
     * @Assert\Email(message="Please enter valid email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your first name must be at least 2 characters long.",
     *      maxMessage = "Your first name cannot be longer than 30 characters.")
     * @Assert\Regex("/^[A-Z]{1}[a-z]{1,29}$/",
     *     message="Your first name should start with capital letter and contains only letters")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255 )
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your last name must be at least 2 characters long.",
     *      maxMessage = "Your last name cannot be longer than 30 characters.")
     * @Assert\Regex("/^[A-Z]{1}[a-z]{1,29}$/",
     *     message="Your last name should start with capital letter and contains only letters.")
     */
    private $lastName;

    /**
     * @var ArrayCollection|Car[]
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Car", mappedBy="owner" )
     */
    private $cars;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="TopCarBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     */
    private $roles;

    /**
     * @var ArrayCollection|Comment[]
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar;

    /**
     * @var ArrayCollection|Message[]
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Message", mappedBy="sender")
     */
    private $sentMessages;

    /**
     * @var ArrayCollection|Message[]
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Message", mappedBy="recipient")
     */
    private $receivedMessages;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->avatar = self::DEFAULT_AVATAR;
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email.
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
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
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
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role) {
            /** @var Role $role */
            $stringRoles[] = $role->getRole();
        }
        return $stringRoles;
    }

    /**
     *
     *
     * @param $role
     * @return User
     */
    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {

    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * @return ArrayCollection|Car[]
     */
    public function getCars()
    {
        return $this->cars;
    }

    /**
     * @param Car $car
     * @return User
     */
    public function addCar(Car $car)
    {
        $this->cars[] = $car;

        return $this;
    }

    /**
     * @param Car $car
     * @return bool
     */
    public function isOwner(Car $car)
    {
        return $car->getOwnerId() == $this->getId();
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function isAuthor(Comment $comment)
    {
        return $comment->getAuthor()->getId() == $this->getId();
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * @return ArrayCollection|Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection|Comment[] $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return ArrayCollection|Message[]
     */
    public function getSentMessages()
    {
        return $this->sentMessages;
    }

    /**
     * @param ArrayCollection|Message[] $sentMessages
     */
    public function setSentMessages($sentMessages)
    {
        $this->sentMessages = $sentMessages;
    }

    /**
     * @return ArrayCollection|Message[]
     */
    public function getReceivedMessages()
    {
        return $this->receivedMessages;
    }

    /**
     * @param ArrayCollection|Message[] $receivedMessages
     */
    public function setReceivedMessages($receivedMessages)
    {
        $this->receivedMessages = $receivedMessages;
    }
}

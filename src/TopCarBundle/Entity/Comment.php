<?php

namespace TopCarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="TopCarBundle\Repository\Comments\CommentRepository")
 */
class Comment
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
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     * @Assert\NotBlank(message="Comment content can't be blank!")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\Car", inversedBy="comments")
     */
    private $car;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\User", inversedBy="comments")
     */
    private $author;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_edited", type="boolean")
     */
    private $isEdited;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->isEdited = false;
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
     * Set content.
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return Comment
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return Car
     */
    public function getCar(): Car
    {
        return $this->car;
    }

    /**
     * @param Car $car
     */
    public function setCar(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @return bool
     */
    public function isEdited()
    {
        return $this->isEdited;
    }

    /**
     * @param bool $isEdited
     */
    public function setIsEdited($isEdited)
    {
        $this->isEdited = $isEdited;
    }
}

<?php

namespace TopCarBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Car
 *
 * @ORM\Table(name="cars")
 * @ORM\Entity(repositoryClass="TopCarBundle\Repository\Cars\CarRepository")
 */
class Car
{
    const INITIAL_VIEW_COUNT = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\Brand", inversedBy="cars")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id" )
     */
    private $brand;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Model should not be blank!")
     *
     * @ORM\Column(name="model", type="string", length=255)
     */
    private $model;

    /**
     * @var Body
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\Body", inversedBy="cars")
     * @ORM\JoinColumn(name="body_id", referencedColumnName="id")
     */
    private $body;

    /**
     * @var string
     * @Assert\NotBlank(message="Year should not be blank!")
     * @Assert\Regex("/^(19|20)\d{2}$/", message="Please enter valid year.")
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var Fuel
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\Fuel", inversedBy="cars")
     */
    private $fuel;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Power should not be blank!")
     * @Assert\Regex("/^\d{2,4}$/", message="Please enter valid power.")
     * @ORM\Column(name="power", type="integer")
     */
    private $power;

    /**
     * @var int
     * @Assert\NotBlank(message="Top Speed should not be blank!")
     * @Assert\Regex("/^\d{2,4}$/", message="Please enter valid top speed.")
     * @ORM\Column(name="topSpeed", type="integer")
     */
    private $topSpeed;

    /**
     * @var float
     * @Assert\NotBlank(message="Acceleration should not be blank!")
     * @Assert\Regex("/^[0-9]*[.]?[0-9]{1,2}$/", message="Please enter valid acceleration")
     * @ORM\Column(name="acceleration", type="float", scale=2)
     */
    private $acceleration;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    private $ownerId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="TopCarBundle\Entity\User", inversedBy="cars")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var integer
     *
     * @ORM\Column(name="view_count", type="integer")
     */
    private $viewCount;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text")
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var ArrayCollection|Comment[]
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Comment", mappedBy="car", cascade={"remove"})
     */
    private $comments;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();

        $this->comments = new ArrayCollection();

        $this->viewCount = self::INITIAL_VIEW_COUNT;
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
     * @param Brand $brand
     * @return Brand
     */
    public function setBrand(Brand $brand)
    {
        return $this->brand = $brand;

    }

    /**
     * Get brand.
     *
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return Car
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Body $body
     * @return Body
     */
    public function setBody(Body $body)
    {
        return $this->body = $body;
    }

    /**
     * Get body.
     *
     * @return Body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set year.
     *
     * @param \DateTime $year
     *
     * @return Car
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return \DateTime
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param Fuel $fuel
     * @return Fuel
     */
    public function setFuel(Fuel $fuel)
    {
        return $this->fuel = $fuel;
    }

    /**
     * Get fuel.
     *
     * @return Fuel
     */
    public function getFuel()
    {
        return $this->fuel;
    }

    /**
     * Set power.
     *
     * @param int $power
     *
     * @return Car
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power.
     *
     * @return int
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set topSpeed.
     *
     * @param int $topSpeed
     *
     * @return Car
     */
    public function setTopSpeed($topSpeed)
    {
        $this->topSpeed = $topSpeed;

        return $this;
    }

    /**
     * Get topSpeed.
     *
     * @return int
     */
    public function getTopSpeed()
    {
        return $this->topSpeed;
    }

    /**
     * Set acceleration.
     *
     * @param float $acceleration
     *
     * @return Car
     */
    public function setAcceleration($acceleration)
    {
        $this->acceleration = $acceleration;

        return $this;
    }

    /**
     * Get acceleration.
     *
     * @return float
     */
    public function getAcceleration()
    {
        return $this->acceleration;
    }


    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param $ownerId
     * @return Car
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return Car
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount(int $viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded(): \DateTime
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     */
    public function setDateAdded(\DateTime $dateAdded)
    {
        $this->dateAdded = $dateAdded;
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


}

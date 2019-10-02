<?php

namespace TopCarBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fuel
 *
 * @ORM\Table(name="fuels")
 * @ORM\Entity(repositoryClass="TopCarBundle\Repository\Cars\FuelRepository")
 */
class Fuel
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
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TopCarBundle\Entity\Car", mappedBy="fuel")
     */
    private $cars;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
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
     * Set type.
     *
     * @param string $type
     *
     * @return Fuel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ArrayCollection
     */
    public function getCars(): ArrayCollection
    {
        return $this->cars;
    }

    /**
     * @param ArrayCollection $cars
     */
    public function setCars(ArrayCollection $cars): void
    {
        $this->cars = $cars;
    }

    public function __toString()
    {
        return (string)$this->type;
    }
}

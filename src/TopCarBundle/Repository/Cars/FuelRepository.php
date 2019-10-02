<?php

namespace TopCarBundle\Repository\Cars;

use Doctrine\ORM\EntityManagerInterface;
use TopCarBundle\Entity\Fuel;
use Doctrine\ORM\Mapping;

/**
 * FuelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FuelRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metadata = null)
    {
        parent::__construct($em,
            $metadata == null ?
                new Mapping\ClassMetadata(Fuel::class) :
                $metadata
        );
    }
}

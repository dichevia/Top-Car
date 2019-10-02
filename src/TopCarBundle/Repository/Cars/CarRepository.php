<?php

namespace TopCarBundle\Repository\Cars;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\ORMException;
use TopCarBundle\Entity\Car;

/**
 * CarRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CarRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metadata = null)
    {
        parent::__construct($em,
            $metadata == null ?
                new Mapping\ClassMetadata(Car::class) :
                $metadata
        );
    }

    public function insert($car)
    {
        try {
            $this->_em->persist($car);
            $this->_em->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }


    public function remove($car)
    {
        try {
            $this->_em->remove($car);
            $this->_em->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    public function update($car)
    {
        try {
            $this->_em->merge($car);
            $this->_em->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    public function getFirstMostViewed()
    {
        return $this->createQueryBuilder('car')
            ->orderBy('car.viewCount', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    public function getAllByDate($page, $rpp)
    {
        $results = $this->createQueryBuilder('car')
            ->orderBy('car.dateAdded', 'DESC')
            ->setFirstResult(($page - 1) * $rpp)
            ->setMaxResults($rpp)
            ->getQuery()
            ->getResult();
        $count = $this->createQueryBuilder('car')
            ->select('COUNT(car)')
            ->getQuery()
            ->getSingleScalarResult();

        return [$results, $count];
    }


    public function getAllByBody($type, $page, $rpp)
    {
        $results = $this->createQueryBuilder('c')
            ->addSelect('b')
            ->innerJoin("c.body", 'b')
            ->where('b.type=:type')
            ->setParameter('type', $type)
            ->setFirstResult(($page - 1) * $rpp)
            ->setMaxResults($rpp)
            ->getQuery()
            ->getResult();
        $count = count($results);

        return [$results, $count];
    }

    public function getAllByBrand($brand, $page, $rpp)
    {
        $results = $this->createQueryBuilder('c')
            ->addSelect('b')
            ->innerJoin("c.brand", 'b')
            ->where('b.name=:brand')
            ->setParameter('brand', $brand)
            ->setFirstResult(($page - 1) * $rpp)
            ->setMaxResults($rpp)
            ->getQuery()
            ->getResult();
        $count = count($results);

        return [$results, $count];
    }

}
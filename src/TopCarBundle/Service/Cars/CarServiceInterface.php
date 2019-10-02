<?php


namespace TopCarBundle\Service\Cars;

use TopCarBundle\Entity\Car;

interface CarServiceInterface
{
    public function save(Car $car);

    public function edit(Car $car);

    public function remove(Car $car);

    public function updateViews(Car $car);

    public function findAllByDate($page, $rpp);

    public function findOneById($id);

    public function findAllByOwnerId();

    public function findFirstMostViewed();

    public function findAllByBody($type, $page, $rpp);

    public function findAllByBrand($brand, $page, $rpp);
}
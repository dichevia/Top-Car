<?php


namespace TopCarBundle\Service\Users;


use TopCarBundle\Entity\User;

interface UserServiceInterface
{
    public function findOneByEmail($email);

    public function save(User $user);

    public function merge(User $user);

    public function findOneById ($id);

    public function findOne ($user);

    public function currentUser();

}
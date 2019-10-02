<?php


namespace TopCarBundle\Service\Users;


use Symfony\Component\Security\Core\Security;
use TopCarBundle\Entity\User;
use TopCarBundle\Repository\Roles\RoleRepository;
use TopCarBundle\Repository\Users\UserRepository;
use TopCarBundle\Service\Encryption\ArgonEncryption;

class UserService implements UserServiceInterface
{
    private $userRepository;

    private $security;

    private $encryptionService;

    private $roleRepository;

    public function __construct(UserRepository $userRepository,
                                Security $security,
                                ArgonEncryption $encryptionService,
                                RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
        $this->roleRepository = $roleRepository;
    }

    public function findOneByEmail($email)
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function save(User $user)
    {
        $passwordHash = $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        $userRole = $this->roleRepository->findOneBy(['name'=>'ROLE_USER']);
        $user->addRole($userRole);

        return $this->userRepository->insert($user);
    }

    public function findOneById($id)
    {
        return $this->userRepository->find($id);
    }

    public function findOne($user)
    {
        return $this->userRepository->find($user);
    }

    public function currentUser()
    {
        return $this->security->getUser();
    }


    public function merge(User $user)
    {
        return $this->userRepository->update($user);
    }
}
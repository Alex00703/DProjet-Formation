<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\Users;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\List_;

class RoleService
{

    public $ADMIN_PERMISSION = "ADMIN_PERMISSION";
    public $MOD_PERMISSION = "MOD_PERMISSION";
    public $USER_PERMISSION = "USER_PERMISSION";


    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function getRole(Users $user, RoleRepository $roleRepository): Role
    {
        return $roleRepository->findOneByName($user->getRole());
    }

    public function isAdmin(Users $user, RoleRepository $roleRepository): bool
    {
        $role = $this->getRole($user, $roleRepository);
        $permissions = $role->getPermissions();
        return in_array($this->ADMIN_PERMISSION, $permissions) ? true : false;
    }

    public function isMod(Users $user, RoleRepository $roleRepository): bool
    {
        $role = $this->getRole($user, $roleRepository);
        $permissions[] = $role->getPermissions();
        return in_array($this->MOD_PERMISSION, $permissions) || in_array($this->ADMIN_PERMISSION, $permissions) ? true : false;
    }

}

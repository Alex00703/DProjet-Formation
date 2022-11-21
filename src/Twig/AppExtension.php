<?php

namespace App\Twig;

use App\Entity\Users;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{

    public Users $user;
    public RoleRepository $roleRepository;

    public function __construct(TokenStorageInterface $tokenStorage, RoleRepository $roleRepository)
    {
        try {
            $this->user = $tokenStorage->getToken()->getUser();
            $this->roleRepository = $roleRepository;
        } catch (\Throwable $th) {
            unset($user);
        }
    }

    public function getGlobals(): array
	{
		 return [
			'title' => 'Silezia',
            'onlinePlayers' => $this->getOnlinePlayers(),
            'coins' => $this->getPlayerCoins(),
            'role' => isset($this->user) ? $this->roleRepository->findOneByName($this->user->getRole())->getDisplayname() : "INVITE",
		];
	}

    public function getOnlinePlayers(): int
    {
        return 0; // TEMPORARY
    }

    public function getPlayerCoins(): int
    {
        return isset($this->user) ? $this->user->getCoins() : 0;
    }
}
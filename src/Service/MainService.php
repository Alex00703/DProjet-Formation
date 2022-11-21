<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class MainService
{

    


    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function getRegisteredPlayers(): int
    {
        $em = $this->em->getDoctrine()->getManager();
        
        $usersRepo = $em->getRepository(Users::class);
        
        $totalUsers = $usersRepo->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $totalUsers;
    }

}

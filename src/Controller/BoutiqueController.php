<?php
namespace App\Controller;

use App\Entity\Users;
use App\Repository\ArticleRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BoutiqueController extends AbstractController
{

    public function boutique(): Response
    {

        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('main/boutique.html.twig', [
        ]);
    }


}
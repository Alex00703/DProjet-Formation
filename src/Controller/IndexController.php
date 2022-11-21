<?php
namespace App\Controller;

use App\Entity\Users;
use App\Repository\ArticleRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{

    public function index(ArticleRepository $articleRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $articles = $articleRepository->findFourLastArticles();

        $usersRepo = $em->getRepository(Users::class);
        
        $totalUsers = $usersRepo->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('main/index.html.twig', [
            'totalUsers' => $totalUsers,
            'articles' => $articles,
        ]);
    }

    public function article(int $id, ArticleRepository $articleRepository): Response
    {

        $article = $articleRepository->findOneById($id);

        return $this->render('main/article.html.twig', [
            'article' => $article,
        ]);
    }

}
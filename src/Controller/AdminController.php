<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Role;
use App\Form\CreateArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\RoleRepository;
use App\Repository\UsersRepository;
use App\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{

    public function admin(RoleService $roleService, RoleRepository $roleRepository): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        return $this->render('panels/admin/admin.html.twig', [
        ]);
    }

    public function articles(ArticleRepository $articleRepository, RoleService $roleService, RoleRepository $roleRepository): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }

        $articles = $articleRepository->findAllArticles();
        return $this->render('panels/admin/articles/articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    public function create_article(Request $request, EntityManagerInterface $entityManager, RoleService $roleService, RoleRepository $roleRepository): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        $article = new Article();
        $form = $this->createForm(CreateArticleFormType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $title = $form->get('title')->getData();
            $content = $form->get('content')->getData();
            $image = $form->get('image')->getData();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setImage($image);
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('admin.articles');
        }
        return $this->render('panels/admin/articles/create_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit_article(int $id, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager, RoleService $roleService, RoleRepository $roleRepository): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        $deleteForm = $this->createFormBuilder() ->add('submit', SubmitType::class, ['label' => 'Supprimer'])->getForm();
        $deleteForm->handleRequest($request);
        $article = $articleRepository->findOneById($id);
        $form = $this->createForm(CreateArticleFormType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $title = $form->get('title')->getData();
            $content = $form->get('content')->getData();
            $image = $form->get('image')->getData();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setImage($image);
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('admin.articles');
        }
        if($deleteForm->isSubmitted() && $deleteForm->isValid()){
            $articleRepository->remove($article, true);
            return $this->redirectToRoute('admin.articles');
        }
        return $this->render('panels/admin/articles/edit_article.html.twig', [
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView(),
            'article' => $article,
        ]);
    }

    public function users(UsersRepository $usersRepository, RoleRepository $roleRepository, RoleService $roleService){

        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }

        $users = $usersRepository->findAll();

        return $this->render('panels/admin/users/users.html.twig', [
            'users' => $users,
        ]);
    }

    public function edit_user(int $id, Request $request, UsersRepository $usersRepository, RoleRepository $roleRepository, EntityManagerInterface $entityManager, RoleService $roleService): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }

        $choices = array();

        foreach($roleRepository->findAll() as $role){
            $choices[$role->getDisplayname()] = $role->getRole();
        }

        $user = $usersRepository->findOneById($id);

        $editForm = $this->createFormBuilder() 
            ->add('name', TextType::class, ['attr' => ['disabled' => 'disabled', 'value' => $user->getUsername()]])
            ->add('email', EmailType::class, ['attr' => ['disabled' => 'disabled', 'value' => $user->getEmail()]] )
            ->add('coins', IntegerType::class, ['attr' => ['value' => $user->getCoins()]])
            ->add('role', ChoiceType::class, [
                'attr' => ['value' => $user->getRole()],
                'choices' => [
                    $choices
                ],
                'data' => $user->getRole(),
            ])
            ->getForm();

        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid()){

            $user->setCoins($editForm->get('coins')->getData());
            $user->setRole($roleRepository->findOneByName($editForm->get('role')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin.users');

        }

       

        return $this->render('panels/admin/users/edit_user.html.twig', [
            'editForm' => $editForm->createView(),
        ]);
    }

    public function roles(RoleRepository $roleRepository, RoleService $roleService): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        $roles = $roleRepository->findAll();

        return $this->render('panels/admin/roles/roles.html.twig', [
            'roles' => $roles,
        ]);
    }

    public function create_role(Request $request, EntityManagerInterface $entityManager, RoleService $roleService, RoleRepository $roleRepository): Response
    {
        $user = $this->getUser();


        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        $createForm = $this->createFormBuilder() 
            ->add('role', TextType::class)
            ->add('name', TextType::class)
            ->add('permissions', TextType::class) //explode(“ “, “liste de permissions")
            ->getForm();

        $createForm->handleRequest($request);

        if($createForm->isSubmitted() && $createForm->isValid()){
            $role = new Role();
            
            $role->setRole($createForm->get('role')->getData());
            $role->setDisplayname($createForm->get('name')->getData());
            $role->setPermissions(explode(" ", $createForm->get('permissions')->getData()));

            $entityManager->persist($role);
            $entityManager->flush();            

            return $this->redirectToRoute('admin.roles');
        }

        return $this->render('panels/admin/roles/create_role.html.twig', [
            'createForm' => $createForm->createView(),
        ]);
    }

    public function edit_role(int $id, Request $request, RoleRepository $roleRepository, EntityManagerInterface $entityManager, RoleService $roleService): Response
    {
        $user = $this->getUser();
        if(!$this->getUser() || !$roleService->isAdmin($user, $roleRepository)){
            return $this->redirectToRoute('index');
        }
        $deleteForm = $this->createFormBuilder() ->add('submit', SubmitType::class, ['label' => 'Supprimer'])->getForm();
        $deleteForm->handleRequest($request);

        $role = $roleRepository->findOneById($id);
        $permissions = null;

        foreach($role->getPermissions() as $perm){
            $permissions = $permissions . " " . $perm;
        }

        $editForm = $this->createFormBuilder() 
            ->add('role', TextType::class, ['attr' => ['value' => $role->getRole()]])
            ->add('name', TextType::class, ['attr' => ['value' => $role->getDisplayname()]])
            ->add('permissions', TextType::class, ['attr' => ['value' => $permissions]])
            ->getForm();

        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid()){

            $role->setRole($editForm->get('role')->getData());
            $role->setDisplayname($editForm->get('name')->getData());
            $role->setPermissions(explode(" ", $editForm->get('permissions')->getData()));

            $entityManager->persist($role);
            $entityManager->flush();            

            return $this->redirectToRoute('admin.roles');
        }

        if($deleteForm->isSubmitted() && $deleteForm->isValid()){
            $roleRepository->remove($role, true);
            return $this->redirectToRoute('admin.roles');
        }

        return $this->render('panels/admin/roles/edit_role.html.twig', [
            'editForm' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        ]);
    }
}
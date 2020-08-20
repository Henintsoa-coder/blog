<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{   
    
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() 
    {
        $title = "Bienvenue ici les amis !";
        $age = 29;

        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
            'title' => $title,
            'age' => $age
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager) {
        if ($article === null) {
            $article = new Article();
        }
        
        /*
            $form = $this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();
        */

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        dump($article);
        
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     * 
     * Permet de montrer un seul article, mais aussi tous ses commentaires
     * 
     */
    public function show(Article $article, Request $request=null,  EntityManagerInterface $manager)
    {
        $user = $this->getUser()? $this->getUser()->getUsername() : '';
        
        if($request){
            $comment = new Comment();    
        
            $formComment = $this->createForm(CommentType::class, $comment);

            //dump($request);

            $formComment->handleRequest($request);

            if ($formComment->isSubmitted() && $formComment->isValid()) {
                
                //Mise en place des différentes propriétés de l'entity Comment
                $comment->setAuthor($this->getUser()->getUsername());
                $comment->setCreatedAt(new \DateTime());
                
                $article->addComment($comment);

                $manager->persist($comment);
               // $manager->persist($article);

                $manager->flush();

                return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
            }
        }
        
        return $this->render('blog/show.html.twig', [
            //'id_article' => $article->getId(),
            'user' => $user,
            'article' => $article,
            'formComment' => $formComment->createView()
        ]);
        
    }

    
}

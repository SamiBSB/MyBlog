<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilder;
use App\Service\MessageGenerator;
use App\Service\StringUtils;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //$repo=$this->getDoctrine()->getRepository(Article::Class);

        $articles=$repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'Sami',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(StringUtils $str)
    {
        //$this->addFlash('salut','salut ');
        return $this->render('blog/home.html.twig', [
        'title'=>$str->capitalise("saMIiiNNoO"),
        'age'=>37
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show", requirements={"id": "\d+" })
     */
    public function show(ArticleRepository $repo,$id,EntityManagerInterface $entityManager,Request $request)
    {   
        $article=$repo->find($id);
        $comment = new Comment();
        $form=$this->createForm(CommentType::Class,$comment);
        $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success','Le message a été enregistré avec succés');
            return $this->redirectToRoute('blog_show',[
                'id' => $article->getId()]);
        }
            return $this->render('blog/show.html.twig',[
             'article' => $article,
             'comment'=>$comment,
             'commentform' => $form->createView()
            ]);
    }

/**
 * @Route("/blog/new", name="blog_create")
 * @Route("/blog/{id}/edit", name="blog_edit")
 */
public function create(MessageGenerator $msg,Article $article=null,EntityManagerInterface $entityManager,Request $request){
    
    if(!$article){
    $article = new Article();
}
    $form=$this->createForm(ArticleType::Class,$article);
    $form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {

    if(!$article->getId()){
        $article->setCreatedAt(new \DateTime());
    }
        $entityManager->persist($article);
        $entityManager->flush();
        //$this->addFlash('success','L article a été enregistré avec succés');
        $message = $msg->getHappyMessage();
        $this->addFlash('success', $message);

        return $this->redirectToRoute('blog_show',[
            'id' => $article->getId()]);
    }
return $this->render('blog/create.html.twig',[
    'form' => $form->createView(),
    'editMode' => $article->getId()!==null
]);

}

/**
 *  @Route("/blog/{id}/delete", name="blog_delete", requirements={"id": "\d+" } )
 */
public function delete(EntityManagerInterface $entityManager,Request $request,$id,Article $article){

    
    if(empty($article)){

        $this->addFlash('warning','Impossible de supprimé cet article');

    }else{

        $entityManager->remove($article);

        $entityManager->flush();

        $this->addFlash('success','L article a été supprimé avec succés');

        }
        return $this->redirectToRoute('blog');
    }
}

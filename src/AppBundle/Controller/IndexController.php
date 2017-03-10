<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Commentaires;
use AppBundle\Form\ContactType;
use AppBundle\Form\NewCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{

    /**
     * @Route("/{page}", name="homepage", requirements={"page" : "\d+"})
     */
    public function indexAction($page = 0)
    {
        $articles = $this->get('app.show_articles')->getArticles($page);
        $paginationData = $this->get('app.show_articles')->handlePagination($page);
        return $this->render('index_controller/index.html.twig', [
            'articles' => $articles,
            'data' => $paginationData
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction()
    {
        return $this->render('index_controller/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            $this->get('app.send_mail')->sendContactMail($data);
            $this->addFlash('success', 'Nous avons reçu votre mail et vous répondrons dans les plus brefs délais.');
        }
        return $this->render('index_controller/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show_article/{slug}", name="show_article")
     */
    public function showArticleAction(Article $article)
    {
        return $this->render('index_controller/show_article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/new/comment/{slug}", name="new_comment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function newCommentAction(Article $article, Request $request)
    {
        $form = $this->createForm(NewCommentType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            $this->get('app.manager.comments_manager')->persistNewComment($data, $article);
            $this->addFlash('success', 'Votre commentaire a été enregistré.');
            return new RedirectResponse($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
        }
        return $this->render('index_controller/new_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/answer/comment/{id}", name="answer_comment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function answerCommentAction(Commentaires $commentaire, Request $request)
    {
        $form = $this->createForm(NewCommentType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            $article = $commentaire->getArticle();
            $this->get('app.manager.comments_manager')->answerComment($commentaire, $data, $article);
            $this->addFlash('success', 'Votre commentaire a été enregistré.');
            return new RedirectResponse($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
        }
        return $this->render('index_controller/new_comment.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/report/comment/{id}", name="report_comment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function reportCommentAction(Commentaires $commentaire)
    {
        $article = $commentaire->getArticle();
        $this->get('app.manager.comments_manager')->reportComment($commentaire);
        $this->addFlash('success', 'Le commentaire a été signalé.');
        return new RedirectResponse($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
    }
}

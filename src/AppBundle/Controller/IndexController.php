<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Commentaires;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\NewCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{

    /**
     * @Route("/{page}", name="homepage", requirements={"page" : "\d+"})
     * @Method("GET")
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
     * @Method("GET")
     */
    public function aboutAction()
    {
        return $this->render('index_controller/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @Method({"GET", "POST"})
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
            return new RedirectResponse($this->generateUrl('homepage'));
        }
        return $this->render('index_controller/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show_article/{slug}", name="show_article")
     * @Method({"GET", "POST"})
     */
    public function showArticleAction(Article $article, Request $request)
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
        return $this->render('index_controller/show_article.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/answer/comment/{id}", name="answer_comment")
     * @Method({"GET", "POST"})
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
        return (!$request->isXmlHttpRequest()) ? $this->render('index_controller/answer_comment.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView()
        ]) : $this->render('index_controller/answer_comment_AJAX.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/report/comment/{id}", name="report_comment")
     * @Method("GET")
     * @Security("is_granted('ROLE_USER')")
     */
    public function reportCommentAction(Commentaires $commentaire)
    {
        $article = $commentaire->getArticle();
        $this->get('app.manager.comments_manager')->reportComment($commentaire);
        $this->addFlash('success', 'Le commentaire a été signalé.');
        return new RedirectResponse($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
    }

    /**
     * @Route("/search", name="search")
     * @Method("POST")
     */
    public function searchAction(Request $request)
    {
        $data = $request->get('user_input');
        if(!$data)
        {
            $this->addFlash('warning', 'Vous devez spécifier un terme à rechercher.');
        }
        $searchResults = $this->get('app.show_articles')->getSearchResults($data);
        if(!$searchResults)
        {
            $this->addFlash('warning', 'Aucun article n\'a été trouvé.');
        }
        if(!$data || !$searchResults) return new RedirectResponse($request->headers->get('referer'));
        return $this->render('index_controller/search_results.html.twig', [
            'articles' => $searchResults
        ]);
    }
}

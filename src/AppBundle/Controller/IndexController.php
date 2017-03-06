<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/show_article/{titre}", name="show_article")
     */
    public function showArticleAction(Article $titre)
    {

    }
}

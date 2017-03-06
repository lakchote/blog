<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\AdminCreateArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function loginAdminAction()
    {
        return $this->render('admin_controller/home.html.twig');
    }

    /**
     * @Route("/admin/new/article", name="admin_create_article")
     */
    public function createArticleAction(Request $request)
    {
        $form = $this->createForm(AdminCreateArticleType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            $data->setDatePublication(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'L\'article a été créé.');
            return new RedirectResponse($this->generateUrl('admin_home'));
        }
        return $this->render('admin_controller/create_article.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/show/articles/{page}", name="admin_show_articles")
     */
    public function showArticlesAction($page = 0)
    {
        $articles = $this->get('app.show_articles')->getArticles($page);
        $paginationData = $this->get('app.show_articles')->handlePagination($page);
        return $this->render('admin_controller/show_articles.html.twig', [
            'articles' => $articles,
            'data' => $paginationData
        ]);
    }

    /**
     * @Route("/admin/modify/article/{id}", name="admin_modify_article")
     */
    public function modifyArticleAction(Article $article, Request $request)
    {
        $form = $this->createForm(AdminCreateArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
        }
        return $this->render('admin_controller/modify_article.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/article/{id}", name="admin_delete_article")
     */
    public function deleteArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'L\'article a été supprimé.');
        return new RedirectResponse($this->generateUrl('admin_show_articles'));
    }
}

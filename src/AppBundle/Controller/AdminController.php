<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Commentaires;
use AppBundle\Form\Type\AdminArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Method("GET")
     */
    public function homeAdminAction()
    {
        $resultFlaggedComments = $this->get('app.show_comments')->countFlaggedComments();
        $resultNewComments = $this->get('app.show_comments')->countUnreadComments();
        return $this->render('admin_controller/home.html.twig', [
            'flaggedComments' => $resultFlaggedComments,
            'newComments' => $resultNewComments
        ]);
    }

    /**
     * @Route("/admin/new/article", name="admin_create_article")
     * @Method({"GET", "POST"})
     */
    public function createArticleAction(Request $request)
    {
        $form = $this->createForm(AdminArticleType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            $this->get('app.manager.articles_manager')->persistArticle($data);
            $this->addFlash('success', 'L\'article a été créé.');
            return new RedirectResponse($this->generateUrl('admin_home'));
        }
        return $this->render('admin_controller/create_article.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/show/articles/{page}", name="admin_show_articles")
     * @Method("GET")
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
     * @Route("/admin/modify/article/{id}/{ref}", name="admin_modify_article")
     * @Method({"GET", "POST"})
     */
    public function modifyArticleAction(Article $article, $ref = null,  Request $request)
    {
        $form = $this->createForm(AdminArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $this->get('app.manager.articles_manager')->persistArticle($article);
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
            return new RedirectResponse($this->generateUrl('admin_modify_article', ['id' => $article->getId()]));
        }
        return $this->render('admin_controller/modify_article.html.twig', [
            'form' => $form->createView(),
            'ref' => $ref
        ]);
    }

    /**
     * @Route("/admin/delete/article/{id}", name="admin_delete_article")
     * @Method("GET")
     */
    public function deleteArticleAction(Article $article)
    {
        $this->get('app.manager.articles_manager')->removeArticle($article);
        $this->addFlash('success', 'L\'article a été supprimé.');
        return new RedirectResponse($this->generateUrl('admin_show_articles'));
    }

    /**
     * @Route("/admin/delete_comment/{id}", name="admin_delete_comment")
     * @Method("GET")
     */
    public function deleteCommentAction(Commentaires $commentaire, Request $request)
    {
        $this->get('app.manager.comments_manager')->deleteComment($commentaire);
        $this->addFlash('success', 'Le commentaire a été supprimé.');
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/reset_comment/{id}", name="admin_reset_comment_status")
     * @Method("GET")
     */
    public function resetCommentStatusAction(Commentaires $commentaire)
    {
        $this->get('app.manager.comments_manager')->resetCommentFlaggedStatus($commentaire);
        $this->addFlash('success', 'Le signalement a été supprimé.');
        return new RedirectResponse($this->generateUrl('admin_show_flagged_comments'));
    }

    /**
     * @Route("/admin/show/flagged/comments/{page}", name="admin_show_flagged_comments")
     * @Method("GET")
     */
    public function showFlaggedCommentsAction($page = 0)
    {
        $paginationData = $this->get('app.show_comments')->handlePagination($page, 'flagged');
        $commentaires = $this->get('app.show_comments')->getFlaggedComments($page);
        return $this->render('admin_controller/show_flagged_comments.html.twig', [
            'commentaires' => $commentaires,
            'data' => $paginationData
        ]);
    }

    /**
     * @Route("/admin/show/comments/{page}", name="admin_show_comments")
     * @Method("GET")
     */
    public function showCommentsAction($page = 0)
    {
        $paginationData = $this->get('app.show_comments')->handlePagination($page);
        $commentaires = $this->get('app.show_comments')->getComments($page);
        $this->get('app.manager.comments_manager')->markCommentsAsRead();
        return $this->render('admin_controller/show_comments.html.twig', [
            'commentaires' => $commentaires,
            'data' => $paginationData
        ]);
    }
}

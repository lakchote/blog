<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ForgottenPasswordType;
use AppBundle\Form\Type\LoginType;
use AppBundle\Form\Type\ProfilType;
use AppBundle\Form\Type\RegisterType;
use AppBundle\Form\Type\ResetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @Method({"GET","POST"})
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if($form->isValid())
        {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->get('security.authentication.guard_handler')->authenticateUserAndHandleSuccess($user, $request, $this->get('app.security.login_form_authenticator'), 'main');
        }
        return $this->render('user_controller/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @Method({"GET", "POST"})
     */
    public function loginAction()
    {
        $authInfo = $this->get('security.authentication_utils');
        $error = $authInfo->getLastAuthenticationError();
        $username = $authInfo->getLastUsername();
        $form = $this->createForm(LoginType::class, [
            '_username' => $username
        ]);
        return $this->render('user_controller/login.html.twig',[
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @Method("GET")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/login/facebook", name="login_facebook")
     * @Method("GET")
     */
    public function loginFacebookAction()
    {
        return $this->redirect($this->get('app.security.login_facebook')->getAuthorizationUrl([
            'scopes' => ['public_profile', 'email']
        ]));
    }

    /**
     * @Route("/login/facebook/check", name="login_facebook_check")
     * @Method("GET")
     */
    public function loginFacebookCheckAction()
    {
    }

    /**
     * @Route("/forgotten_password", name="forgotten_password")
     * @Method({"GET","POST"})
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $email = $form['email']->getData();
            $this->get('app.send_mail')->sendResetPasswordMail($email);
            $this->addFlash('success', 'Un email vous a été envoyé avec les instructions à suivre.');
            return new RedirectResponse($this->generateUrl('homepage'));
        }
        return $this->render('user_controller/forgotten_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_password", name="reset_password")
     * @Method({"GET","POST"})
     */
    public function resetPasswordAction(Request $request)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $data = $form->getData();
            if(!$user = $this->get('app.security.reset_password')->checkIfResetIdMatches($data))
            {
                $error = new FormError('ID incorrect.');
                $form->get('resetId')->addError($error);
                return $this->render('user_controller/reset_password.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            return $this->get('security.authentication.guard_handler')->authenticateUserAndHandleSuccess($user, $request, $this->get('app.security.login_form_authenticator'), 'main');
        }
        return $this->render('user_controller/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profil", name="profil_user")
     * @Method({"GET", "POST", "DELETE"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function profilUserAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('profil_user');
        }
        return $this->render('user_controller/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/image", name="delete_user_photo")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_USER')")
     */
    public function deleteUserPhotoAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $user->deletePhoto();
        $em->persist($user);
        $em->flush();
        return new RedirectResponse($this->generateUrl('profil_user'));
    }
}

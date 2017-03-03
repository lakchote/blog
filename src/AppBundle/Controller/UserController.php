<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ForgottenPasswordType;
use AppBundle\Form\LoginType;
use AppBundle\Form\ProfilType;
use AppBundle\Form\RegisterType;
use AppBundle\Form\ResetPasswordType;
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
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/login/facebook", name="login_facebook")
     */
    public function loginFacebookAction()
    {
        return $this->redirect($this->get('app.security.login_facebook')->getAuthorizationUrl([
            'scopes' => ['public_profile', 'email']
        ]));
    }

    /**
     * @Route("/login/facebook/check", name="login_facebook_check")
     */
    public function loginFacebookCheckAction()
    {
    }

    /**
     * @Route("/forgotten_password", name="forgotten_password")
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
        }
        return $this->render('user_controller/forgotten_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_password", name="reset_password")
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
        }
        return $this->render('user_controller/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/image", name="delete_user_photo")
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

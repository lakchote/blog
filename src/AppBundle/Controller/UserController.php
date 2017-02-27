<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginType;
use AppBundle\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}

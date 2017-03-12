<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 27/02/2017
 * Time: 14:50
 */

namespace AppBundle\Security;


use AppBundle\Form\LoginType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $form;
    private $em;
    private $router;
    private $encoder;

    public function __construct(FormFactoryInterface $form, EntityManager $em, RouterInterface $router, UserPasswordEncoder $encoder)
    {
        $this->form = $form;
        $this->em = $em;
        $this->router = $router;
        $this->encoder = $encoder;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

    public function getCredentials(Request $request)
    {
        $isLoginUrl = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if(!$isLoginUrl) {
            return;
        }
        $form = $this->form->create(LoginType::class);
        $form->handleRequest($request);
        $data = $form->getData();
        $request->getSession()->set(Security::LAST_USERNAME, $data['_username']);
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->em->getRepository('AppBundle:User')->findOneBy(['email' => $credentials['_username']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if($credentials['_password'] !== null) {
            if($this->encoder->isPasswordValid($user, $credentials['_password'])) {
                return true;
            }
        }
        return false;
    }

    public function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 27/02/2017
 * Time: 22:12
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFacebookAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $router;
    private $facebookProvider;

    use TargetPathTrait;

    public function __construct(Facebook $facebookProvider, EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
        $this->facebookProvider = $facebookProvider;
    }

    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() != '/login/facebook/check') {
            return;
        }

        if($code = $request->get('code'))
        {
            return $code;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $accessToken = $this->facebookProvider->getAccessToken(
            'authorization_code',
            ['code' => $credentials]
        );

        /**
         * @var FacebookUser $facebookUser
         */
        $facebookUser = $this->facebookProvider->getResourceOwner($accessToken);

        if(!$user = $this->em->getRepository('AppBundle:User')->findOneBy(['email' => $facebookUser->getEmail()]))
        {
            $user = new User();
            $user->setEmail($facebookUser->getEmail());
            $user->setNom($facebookUser->getLastName());
            $user->setPrenom($facebookUser->getFirstName());
            $this->em->persist($user);
            $this->em->flush();
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = null;
        if($request->getSession() instanceof SessionInterface) {
            $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
           if(!$targetPath) return new RedirectResponse($this->router->generate('homepage'));
        }
        return new RedirectResponse($targetPath);
    }

    public function supportsRememberMe()
    {
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }
}

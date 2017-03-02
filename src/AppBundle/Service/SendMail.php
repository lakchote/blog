<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 01/03/2017
 * Time: 16:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\TwigEngine;

class SendMail
{
    private $mailer;
    private $twig;
    private $em;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $twig, EntityManager $em)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
    }

    public function sendResetPasswordMail($email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        $username = $user->getUsername();
        $resetId = md5(uniqid());
        $user->setResetPassword($resetId);
        $this->em->persist($user);
        $this->em->flush();
        $message = new \Swift_Message();
        $message->setSubject('Rénitialisation de votre mot de passe sur Jean Forteroche')
            ->setFrom('noreply@jeanforteroche.com')
            ->setTo($email)
            ->setBody($this->twig->render('mail/forgotten_password_mail.html.twig', [
                'username' => $username,
                'resetId' => $resetId
            ]), 'text/html');
        $this->mailer->send($message);
    }
}
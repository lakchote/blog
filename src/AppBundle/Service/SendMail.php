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
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendMail
{
    private $mailer;
    private $twig;
    private $em;
    private $container;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $twig, EntityManager $em, ContainerInterface $container)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
        $this->container = $container;
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

    public function sendContactMail($data)
    {
        $message = new \Swift_Message();
        $message
            ->setSubject($data['sujet'])
            ->setFrom($data['email'])
            ->setTo($this->container->getParameter('admin_mail'))
            ->setBody($this->twig->render('mail/contact_mail.html.twig', [
                'sujet' => $data['sujet'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'message' => $data['message']
            ]), 'text/html');
        $this->mailer->send($message);
    }
}
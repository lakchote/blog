<?php

namespace AppBundle\Service;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Twig_Environment;

class SendMail
{
    private $mailer;
    private $twig;
    private $em;
    private $adminMail;

    public function __construct(\Swift_Mailer $mailer, Twig_Environment $twig, EntityManager $em, $adminMail)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
        $this->adminMail = $adminMail;
    }

    public function sendResetPasswordMail($email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        $username = $user->getUsername();
        $resetId = md5(uniqid());
        $user->setResetPassword($resetId);
        $this->em->persist($user);
        $this->em->flush();
        $message = \Swift_Message::newInstance()
            ->setSubject('Rénitialisation de votre mot de passe sur Jean Forteroche')
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
        $message = \Swift_Message::newInstance()
            ->setSubject($data['sujet'])
            ->setFrom($data['email'])
            ->setTo($this->adminMail)
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

<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 02/03/2017
 * Time: 12:24
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class ResetPassword
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function checkIfResetIdMatches($data)
    {
        $user = $this->getUser($data['email']);
        if($user->getResetPassword() !== $data['resetId'])
        {
            return false;
        }
        $this->updateUserPassword($user, $data['plainPassword']);
        return $user;
    }

    private function getUser($email)
    {
        if($user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]))
        {
            return $user;
        }

        return false;
    }

    private function updateUserPassword(User $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $user->setResetPassword(null);
        $this->em->persist($user);
        $this->em->flush();
    }
}
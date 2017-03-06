<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 03/03/2017
 * Time: 21:16
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdminUser implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNom('Admin');
        $user->setPrenom('Root');
        $user->setEmail('root@localhost');
        $user->setPlainPassword('Codingame');
        $user->setRoles('ROLE_ADMIN');
        $manager->persist($user);
        $manager->flush();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 01/03/2017
 * Time: 16:25
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class UsernameExists extends Constraint
{
    public $message = 'Aucun nom d\'utilisateur avec cette email.';
}

<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 02/03/2017
 * Time: 12:45
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class CheckPasswordLength extends Constraint
{
    public $minMessage = "Le mot de passe doit faire 6 caractères au minimum.";
    public $maxMessage = "Le mot de passe doit faire 12 caractères au maximum.";
}

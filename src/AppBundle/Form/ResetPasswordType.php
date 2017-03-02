<?php

namespace AppBundle\Form;

use AppBundle\Validator\Constraints\CheckPasswordLength;
use AppBundle\Validator\Constraints\UsernameExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => array(new NotBlank(), new UsernameExists())
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => array(new NotBlank(), new CheckPasswordLength())
            ])
            ->add('resetId', TextType::class, [
                'label' => 'ID que vous avez reçu par mail'
            ]);
    }
}

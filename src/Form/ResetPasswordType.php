<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //    ->add('email')
            //    ->add('roles')
            //    ->add('password')
            //    ->add('firstName')
            //    ->add('lastName')
            //    ->add('active')
            ->add('newPassword', PasswordType::class, ['attr' => ['placeholder' => 'Nouveau mot de passe']])
            ->add('confirmNewPassword', PasswordType::class, ['attr' => ['placeholder' => 'Confirmer le mot de passe']])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

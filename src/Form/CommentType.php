<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //     ->add('CreatedAt')
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire'

            ])
            ->add('rating', IntegerType::class, [
                'label' => 'note sur 5',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'placeholder' => 'Votre note'
                ]

            ])


            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer le commentaire',
                'attr' => [
                    'class' => 'btn btn-success col-12'
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}

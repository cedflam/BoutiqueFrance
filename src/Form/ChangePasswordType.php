<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'Mon mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => "Veuillez saisir votre mot de passe actuel"
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => "Les mots de passe doivent être identique !",
                'label' => "Nouveau mot de passe",
                'required' => true,
                'first_options' => [
                    'label' => "Nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Saisir le nouveau mot de passe..."
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmation du nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmez le nouveau mot de passe..."
                    ]
                ]
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => "Mon prénom"
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => "Mon nom"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

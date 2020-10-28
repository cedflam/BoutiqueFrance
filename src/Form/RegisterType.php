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
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => "Saisir votre prénom..."
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => "Saisir votre nom..."
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'attr' => [
                    'placeholder' => "Saisir votre email..."
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les mots de passe doivent être identique !",
                'label' => "Votre password",
                'required' => true,
                'first_options' => [
                    'label' => "Mot de passe",
                    'attr' => [
                        'placeholder' => "Saisir un mot de passe..."
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmation mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmez le mot de passe..."
                    ]
                ]
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

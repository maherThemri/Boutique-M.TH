<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                "label" => "Votre prénom",
                "constraints" => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                "attr" => [
                    'placeholder' => "Saisir votre prénom"
                ]
            ])
            ->add('lastName', TextType::class, [
                "label" => "Votre nom",
                "constraints" => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                "attr" => [
                    'placeholder' => "Saisir votre nom"
                ]
            ])
            ->add('email', EmailType::class, [
                "label" => "Votre email",
                "constraints" => new Length([
                    'min' => 2,
                    'max' => 60
                ]),
                "attr" => [
                    'placeholder' => "Saisir votre email"
                ]
            ])
            // ->add('roles')
            ->add('password', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Le mot de passe et la confiramtion doivent etre identique.",
                "label" => "Votre mot de passe",
                "required" => true,
                "first_options" => [
                    "label" => "Mot de passe",
                    "attr" => [
                        "placeholder" => "Saisir votre Mot de passe"
                    ]
                ],
                "second_options" => [
                    "label" => "Confirmation mot de passe",
                    "attr" => [
                        "placeholder" => "Confirmez votre Mot de passe"
                    ]
                ]
            ])
            // ->add('password_confirm', PasswordType::class, [
            //     "label" => "Confirme mot de passe",
            //     "mapped" => false,
            //     "attr" => [
            //         "placeholder" => "Confirmez votre Mot de passe"
            //     ]
            // ])
            ->add('submit', SubmitType::class, [
                "label" => "S'inscrire"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

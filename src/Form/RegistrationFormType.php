<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => ' ',
                'invalid_message' => 'null',

                'attr' => ['placeholder' => 'Pseudo']
            ])
            ->add('email', EmailType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'E-mail']
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  => ['label' => ' ', 'attr' => ['placeholder' => 'Mot de passe']],
                'second_options' => ['label' => ' ', 'attr' => ['placeholder' => 'Confirmation']],
                'attr' => [
                    'autocomplete' => 'new-password', 
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'max' => 4096,
                        ]),
                    ],
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}

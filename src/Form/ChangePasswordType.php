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

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'label' => 'Mon prénom',
            'disabled' => true
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Mon nom',
            'disabled' => true
        ])
        ->add('email', EmailType::class, [
            'label' => 'Mon adresse email',
            'disabled' => true
        ])
        ->add('old_password', PasswordType::class, [
            'mapped' => false,
            'label' => 'Mot de passe actuel',
        ])
        ->add('new_password', RepeatedType::class, [
            'mapped' => false,
            'label' => 'Nouveau mot de passe',
            'constraints' => new Length(['min' => '2', 'max' => '30']),
            'required' => true,
            'invalid_message' => 'Le mot de passe et sa confirmation doivent correspondre.',
            'type' => PasswordType::class,
            'first_options'  => ['label' => 'Nouveau mot de passe'],
            'second_options' => ['label' => 'Confirmer le nouveau mot de passe'],
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Mettre à jour"
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

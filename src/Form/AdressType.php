<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nommez votre adresse'])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'])
            ->add('company', TextType::class, [
                'label' => 'Société (facultatif)',
                'required' => false])
            ->add('address', TextType::class, [
                'label' => 'Adresse'])
            ->add('postal',     TextType::class, [
                'label' => 'Code postal'])
            ->add('city',    TextType::class, [
                'label' => 'Ville'])
            ->add('country', CountryType::class, [
                'label' => 'Pays', 'attr' => [
                    'class' => 'custom-select'
                ]])
            ->add('phone',   TelType::class, [
                'label' => 'Téléphone'])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-info mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}

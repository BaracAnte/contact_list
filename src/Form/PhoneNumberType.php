<?php

namespace App\Form;

use App\Entity\PhoneNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'phone number',
                    'class' => 'klasa'
                ]
            ])
            ->add('label', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'details',
                    'class' => 'klasa'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PhoneNumber::class,
        ]);
    }
}

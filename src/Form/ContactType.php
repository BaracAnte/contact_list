<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname')
            ->add('email')
            ->add('favorite')
            ->add(
                'image',
                FileType::class,
                [
                    'data_class' => null,
                ]
            )
            ->add('phoneNumber', CollectionType::class, [
                'entry_type' => PhoneNumberType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'foo'],
                ],
                'by_reference' => false,
                'allow_add' => true

            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}

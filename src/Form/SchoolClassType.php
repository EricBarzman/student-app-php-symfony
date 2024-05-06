<?php

namespace App\Form;

use App\Entity\SchoolClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('class_name', TextType::class, [
                'label' => 'Name of class*'
            ])
            ->add('teacher_name', TextType::class, [
                'label' => 'Main Teacher\'s name*'
            ])
            ->add('promotion_year')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolClass::class,
        ]);
    }
}

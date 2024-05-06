<?php

namespace App\Form;

use App\Entity\SchoolClass;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                    'required' => false,
                    'label' => 'First name',
                ])
            ->add('lastname', TextType::class, [
                    'required' => false,
                    'label' => 'Last name',
                ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender*',
                'choices' => [
                    'Girl' => 2,
                    'Boy' => 1
                ],
                'expanded' => true
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Birthdate*'
            ])
            ->add('confidential_comments', TextareaType::class, [
                'label' => 'Confidential remarks',
                'required' => false
            ])
            ->add('card_comments', TextType::class, [
                'label' => 'Comments for the student card',
                'required' => false,
                'attr' => ['maxlength' => 70]
            ])
            ->add('address', HiddenType::class, [
                'required' => false
            ])
            ->add('emergency_contact_phone_number', TextType::class, [
                'required' => false,
                'label' => 'Phone number'
            ])
            ->add('emergency_contact_firstname', TextType::class, [
                'label' => 'First name',
                'required' => false
            ])
            ->add('emergency_contact_lastname', TextType::class, [
                'label' => 'Last name',
                'required' => false
            ])
            ->add('emergency_contact_gender', ChoiceType::class, [
                'choices' => [
                    'Mr' => 1,
                    'Mrs' => 2
                ],
                'label' => 'Title'
            ])
            ->add('emergency_contact_relationship_to_student', HiddenType::class, [
                'required' => false
            ])
            ->add('school_class', EntityType::class, [
                'class' => SchoolClass::class,
                'label' => 'School class*',
                'choice_label' => 'class_name',
                'placeholder' => 'Choose a class',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}

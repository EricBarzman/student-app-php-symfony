<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username*'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'required' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'required' => false
            ])
            ->add('pincode', HiddenType::class, [
                'required' => false
            ])
            // ->add('roles', ChoiceType::class, [
            //     'multiple' => true,
            //     'choices' => [
            //         'Read-only' => 'ROLE_USER',
            //         'Read & Create File' => 'ROLE_EDITOR',
            //         'Administrator' => 'ROLE_ADMIN'
            //     ]
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Core\SexeInterface;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewMoniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
//            ->add('roles', ChoiceType::Class, array(
//                'choices' => RoleInterface::DEFINITION
//            ))
            ->add('password')
            ->add('nom')
            ->add('prenom')
//            ->add('sexe', ChoiceType::Class, array(
//                'choices' => SexeInterface::DEFINITION
//            ))
//            ->add('datedenaissance', DateType::Class, array(
//                'widget' => 'choice',
//                'years' => range(date('Y')-16, date('Y')-120)
//            ))
//            ->add('adresse')
//            ->add('codepostal')
//            ->add('ville')
//            ->add('telephone')
//            ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
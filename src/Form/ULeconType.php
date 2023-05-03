<?php

namespace App\Form;

use App\Entity\Lecon;
use App\Entity\User;
use App\Entity\Vehicule;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ULeconType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('heure', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
//                'hours' => range(8, 18, 2),
                'hours' => array(8, 10, 14, 16, 18),
                'minutes' => range(0, 0),
            ])
            //->add('reglee') La valeur par défaut est "0" pour signifier qu'elle n'est pas encore payée.
            ->add('codevehicule', EntityType::class, ['class' => Vehicule::class, 'choice_label' => 'immatriculation'])
            ->add('equipe', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where("u.roles LIKE '%ROLE_MONITEUR%'")
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lecon::class,
        ]);
    }
}

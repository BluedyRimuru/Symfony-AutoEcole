<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Licence;
use App\Entity\Moniteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateobtention', DateType::Class, array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')-100)
            ))
            ->add('codemoniteur',EntityType::class, ['class' => Moniteur::class, 'choice_label' => 'prenom', 'label' => 'Moniteur'])
            ->add('codecategorie',EntityType::class, ['class' => Categorie::class, 'choice_label' => 'libelle', 'label' => 'Categorie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
    }
}

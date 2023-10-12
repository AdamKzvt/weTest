<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ModuleChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('module', ChoiceType::class, [
                'choices'  => $options['modules'],
                'label'=>false,
                'choice_label' => function ($module) {
                    return $module->getNom();
                },
                'placeholder' => 'Choisissez votre module', // <-- Ajoutez ceci
                'choice_value' => function ($module) {
                    return $module ? $module->getId() : '';
                },
            ])
            ->add('simulation_duration', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 40,
                    'value' => 40
                ],
                'label' => 'Durée de la simulation'
            ]);
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'modules' => [],
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\ImportTarget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group', ChoiceType::class, [
                'choices' => $options['groupChoices'],
                'label' => 'Группа, в которую будет осуществляться импорт',
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                ],
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('vkMarketCategory', ChoiceType::class, [
                'choices' => $options['categoryChoices'],
                'label' => 'Основная категория товаров',
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                ],
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Следующий шаг'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'groupChoices' => [],
            'categoryChoices' => [],
            'data_class' => ImportTarget::class,
        ]);
        $resolver->setAllowedTypes('groupChoices', 'array');
        $resolver->setAllowedTypes('categoryChoices', 'array');

    }
}

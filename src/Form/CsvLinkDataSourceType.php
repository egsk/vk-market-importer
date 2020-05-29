<?php

namespace App\Form;

use App\Entity\CsvLinkDataSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvLinkDataSourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sourceLabel', TextType::class, [
                'label' => 'Название источника данных'
            ])
            ->add('sourceUrl', TextType::class, [
                'label' => 'Ссылка на CSV-файл'
            ])
            ->add('delimiter', TextType::class, [
                'label' => 'Разделитель столбцов'
            ])
            ->add('enclosure', TextType::class, [
                'label' => 'Разделитель строк'
            ])
            ->add('uniqueId', TextType::class, [
                'label' => 'Поле, содержащее уникальный идентификатор товара'
            ])
            ->add('name', TextType::class, [
                'label' => 'Поле, содержащее имя товара'
            ])
            ->add('nameHandlePattern', TextType::class, [
                'label' => 'Регулярное выражение для обработки имени',
                'required' => false
            ])
            ->add('url', TextType::class, [
                'label' => 'Поле, содержащее ссылку на товар',
                'required' => false
            ])
            ->add('descriptionPattern', TextareaType::class, [
                'label' => 'Шаблон генерации описания товара, i.e. "%description_column% Ссылка: %url_column%"',
                'required' => false
            ])
            ->add('categoryName', TextType::class, [
                'label' => 'Поле, содержащее название категории в вк. При отсутствии будет использована родительская',
                'required' => false
            ])
            ->add('price', TextType::class, [
                'label' => 'Поле, содержащее цену товара'
            ])
            ->add('photoUrl', TextType::class, [
                'label' => 'Поле, содержащее URL на фотографию товара',
                'required' => false
            ])
            ->add('albumName', TextType::class, [
                'label' => 'Поле, содержащее название подборки',
                'required' => false,
            ])
            ->add('albumHandlePattern', TextType::class, [
                'label' => 'Регулярное выражение для обработки названия подборки',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CsvLinkDataSource::class,
        ]);
    }
}

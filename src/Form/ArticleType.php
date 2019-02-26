<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType ;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
        ->add('id')
        ->add('title')
        ->add('content',  TextareaType::class)
        ->add('loveIts')
        ->add('createdAt', 
              DateTimeType::class,
              [
                'widget'        => 'single_text',
                'format'        => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
                'property_path' => 'createdAt',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'allow_extra_fields' => true,
        ]);
    }
}
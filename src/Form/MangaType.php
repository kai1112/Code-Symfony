<?php

namespace App\Form;

use App\Entity\Manga;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MangaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name',  TextType::class)
            ->add('Description')
            ->add('Author')
            ->add('Price')
            ->add('Quantity')
            ->add('Date', 
            DateType::class,
                [
                    'widget' => 'single_text' 
                ]
            )
            ->add('Genre',
            EntityType::class,
                [
                    'class' => Genre::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false
                ]
            )
            ->add('Image', 
                FileType::class,
                [
                    // 'label' => 'Manga Image',
                    'data_class' => null,
                    'required' => is_null($builder->getData()->getImage())
                ]
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}

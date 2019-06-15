<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add(
                'price',
                IntegerType::class,
                [
                    'constraints' => [new PositiveOrZero()],
                ]
            )
            ->add(
                'images',
                FileType::class,
                [
                    'multiple' => true,
                    'required' => false,
                    'constraints' => [new Image()],
                    'empty_data' => [],
                ]
            )
            ->add(
                'stock',
                IntegerType::class,
                [
                    'required' => false,
                    'constraints' => [new PositiveOrZero()],
                    'empty_data' => null,
                ]
            )
            ->add('category', EntityType::class, ['class' => Category::class]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Article::class,
            ]
        );
    }
}

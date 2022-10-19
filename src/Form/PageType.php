<?php

namespace App\Form;

use App\Entity\Page;
use App\Form\DataTransformer\AuthorToSlugTransformer;
use App\Form\DataTransformer\CategoryToNumberTransformer;
use App\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function __construct(
        private CategoryToNumberTransformer $categoryTransformer,
        private AuthorToSlugTransformer $authorTransformer)
    {

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('status')
            ->add('author', TextType::class)
            ->add('category');

        $builder->get('category')->addModelTransformer($this->categoryTransformer);
        $builder->get('author')->addModelTransformer($this->authorTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}

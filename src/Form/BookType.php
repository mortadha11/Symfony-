<?php

namespace App\Form;
//use <App>
use App\Entity\Book;
use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
            ->add('title')
            ->add('publicationDate', DateType::class, [
                'widget' => 'choice',   // dropdowns like the figure
                'label' => 'Publication date',
                'years' => range(date('Y') - 50, date('Y')),
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Science Fiction' => 'Science Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography',
                ],
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Published',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'username',
                'label' => 'Author',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

<?php
/**
 * Search type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Rating;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('titleSearch', TextType::class, [
                'label' => 'label.titleSearch',
                'required' => false,
                'attr'  => [
                    'placeholder' => 'action.searchTitle',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s]*$/',
                        'message' => 'message.invalid_characters',
                    ]),
                ],
            ])
            ->add('authorSearch', TextType::class, [
                'label' => 'label.authorSearch',
                'required' => false,
                'attr'  => [
                    'placeholder' => 'action.searchAuthor',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s]*$/',
                        'message' => 'message.invalid_characters',
                    ]),
                ],
            ])/*
            ->add(
                'availableSearch',
                ChoiceType::class,
                [
                    'choices' => [
                        'label.available' => true,
                        'label.not_available' => false,
                    ],
                    'expanded' => true,
                    'label' => 'label.availableSearch',
                    'placeholder' => false
                ]
            )*/;

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }
    public function getBlockPrefix(): string
    {
        return '';
    }
}
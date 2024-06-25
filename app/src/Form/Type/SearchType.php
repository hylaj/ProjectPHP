<?php
/**
 * Search type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
/**
 * Class Search type.
 */
class SearchType extends AbstractType
{
    /**
     * Build form.
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod(\Symfony\Component\HttpFoundation\Request::METHOD_GET)
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
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}

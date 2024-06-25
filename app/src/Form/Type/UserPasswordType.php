<?php
/**
 * User Password type.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class User Password Type.
 */
class UserPasswordType extends AbstractType
{

    /**
     * Constructor,
     *
     * @param Security $security
     */
    public function __construct(private readonly Security $security){}

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->security->getUser() === $options['data']) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'label.current_password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'message.enter_current_password',
                        ]),
                    ],
                ]);
        }
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'label.new_password'],
                'second_options' => ['label' => 'label.repeat_password'],
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
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
        return 'user';
    }
}

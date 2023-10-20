<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    /**
     * @param array<mixed> $options
     */
    private function getRoleData(array $options): string
    {
        /** @var User $user */
        $user = $options['data'];
        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return User::ROLE_ADMIN;
        }

        return User::ROLE_USER;
    }

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => $this->translator->trans('app.form.user.name')])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('app.form.user.error_password'),
                'required' => true,
                'first_options' => ['label' => $this->translator->trans('app.form.user.password')],
                'second_options' => ['label' => $this->translator->trans('app.form.user.confirm_password')],
            ])
            ->add('email', EmailType::class, ['label' => $this->translator->trans('app.form.user.email')])
            ->add('roles', ChoiceType::class, [
                'mapped' => false,
                'label' => $this->translator->trans('app.form.user.role'),
                'expanded' => false,
                'multiple' => false,
                'data' => $this->getRoleData($options),
                'choices' => [
                    $this->translator->trans('app.form.user.role_user') => User::ROLE_USER,
                    $this->translator->trans('app.form.user.role_admin') => User::ROLE_ADMIN,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

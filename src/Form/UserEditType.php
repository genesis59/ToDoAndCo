<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEditType extends AbstractType
{
    /**
     * @param array<mixed> $options
     */
    private function getRoleData(array $options): string
    {
        if (in_array(User::ROLE_ADMIN, $options['roles'])) {
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
            ->add('username', TextType::class, [
                'label' => $this->translator->trans('app.form.user.name'),
                'data' => $options['username'],
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'attr' => ['class' => 'mt-1'],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('app.form.user.email'),
                'data' => $options['email'],
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'attr' => ['class' => 'mt-1'],
            ])
            ->add('roles', ChoiceType::class, [
                'mapped' => false,
                'label' => $this->translator->trans('app.form.user.role'),
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'attr' => ['class' => 'form-select mt-1'],
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
            'data_class' => null,
            'username' => null,
            'email' => null,
            'roles' => null,
        ]);
    }
}

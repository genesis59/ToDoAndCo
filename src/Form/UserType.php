<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
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
        ;
    }
}

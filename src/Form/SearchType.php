<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'label' => $this->translator->trans('app.form.user.name'),
                'label_attr' => ['class' => 'visually-hidden'],
                'attr' => [
                    'placeHolder' => $this->translator->trans('app.twig.component.taskBar.search_text'),
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(message: 'validator.search.not_blank'),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('app.twig.component.taskBar.search_label'),
                'attr' => ['class' => 'btn btn-primary mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

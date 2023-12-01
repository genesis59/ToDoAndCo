<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('app.form.task.title'),
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'attr' => ['class' => 'mt-1'],
            ])
            ->add('deadLine', DateTimeType::class, [
                'label' => $this->translator->trans('app.form.task.deadline'),
                'required' => false,
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'widget' => 'single_text',
                'attr' => ['class' => 'mt-1'],
            ])
            ->add('content', TextareaType::class, [
                'label' => $this->translator->trans('app.form.task.content'),
                'label_attr' => ['class' => 'ps-1 mt-4'],
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'data-ck-editor-classic-target' => 'hiddenTaskContent',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}

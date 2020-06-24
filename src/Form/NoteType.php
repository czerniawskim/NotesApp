<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function __construct(\App\Repository\NotesRepository $nr)
    {
        $this->nr = $nr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $note = $this->nr->findOneBy(['id' => $options['id']]);

        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                'data' => $note->getContent()
            ])
            ->add('created', DateTimeType::class, [
                'attr' => [
                    'disabled' => true
                ],
                'label' => 'Created at',
                'widget' => 'single_text',
                'data' => new \DateTime($note->getCreatedAt()->format('d-m-Y H:i:s'))
            ])
            ->add('owner', TextType::class, [
                'attr' => [
                    'disabled' => true,
                    'value' => $note->getOwner()->getName() ? $note->getOwner()->getName() : $note->getOwner()->getUsername()
                ],
                'label' => 'Created by'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('id');
        $resolver->setAllowedTypes('id', [Notes::class, 'int']);
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return 'noteEdit';
    }
}

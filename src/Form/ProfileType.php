<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mediaType', ChoiceType::class, [
                'label' => 'Ton format favori',
                'choices' => [
                    'Anime' => 'anime',
                    'Manga' => 'manga',
                    'Les deux' => 'both'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('progressionAnime', IntegerType::class, [
                'label' => 'Ta progression Anime (Épisode)',
                'required' => false,
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: 1080']
            ])
            ->add('progressionManga', IntegerType::class, [
                'label' => 'Ta progression Manga (Chapitre)',
                'required' => false,
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: 1110']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Mettre à jour mon profil',
                'attr' => ['class' => 'btn-submit']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

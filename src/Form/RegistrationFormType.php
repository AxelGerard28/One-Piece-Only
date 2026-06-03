<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur"
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse Email"
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => "Mot de passe",
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        max: 4096,
                        // max length allowed by Symfony for security reasons
                        minMessage: 'Your password should be at least {{ limit }} characters',
                    ),
                ],
            ])
            ->add('media_type', ChoiceType::class, [
                'choices' => [
                    'L\'anime' => "Anime",
                    'Le manga' => "Manga",
                    'Les deux' => "Both",
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Es tu plus manga, anime ou les deux ?'
            ])
            ->add('progressionAnime', IntegerType::class, [
                'required' => false,
                'label' => 'Votre avancement dans l\'Anime (Numéro d\'épisode)',
                'attr' => ['min' => 0, 'placeholder' => 'Ex: 1100']
            ])
            ->add('progressionManga', IntegerType::class, [
                'required' => false,
                'label' => 'Votre avancement dans le Manga (Numéro de chapitre)',
                'attr' => ['min' => 0, 'placeholder' => 'Ex: 1184']
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

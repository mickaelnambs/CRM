<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class UserType
 * @package App\Form
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Entrez votre adresse email..."
                ]
            ])
            ->add("firstName", TextType::class, [
                "label" => "Prenom(s)",
                "attr" => [
                    "placeholder" => "Entrez votre prenom(s)..."
                ]
            ])
            ->add("lastName", TextType::class, [
                "label" => "Nom de famille",
                "attr" => [
                    "placeholder" => "Entrez votre nom de famille..."
                ]
            ])
            ->add("password", PasswordType::class, [
                "label" => "Mot de passe",
                "attr" => [
                    "placeholder" => "Entrez votre mot de passe..."
                ]
            ])
            ->add("confirmPassword", PasswordType::class, [
                "label" => "Confirmation de mot passe",
                "attr" => [
                    "placeholder" => "Confirmer votre mot de passe..."
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class,
        ]);
    }
}

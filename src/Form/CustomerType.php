<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CustomerType
 * @package App\Form
 */
class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Saisir l'adresse email..."
                ]
            ])
            ->add("firstName", TextType::class, [
                "label" => "Prenom(s)",
                "attr" => [
                    "placeholder" => "Saisir le prenom(s)..."
                ]
            ])
            ->add("lastName", TextType::class, [
                "label" => "Nom de famille",
                "attr" => [
                    "placeholder" => "Saisir le nom de famille..."
                ]
            ])
            ->add('company', TextType::class, [
                "label" => "Entreprise",
                "attr" => [
                    "placeholder" => "Le nom de l'entrepise..."
                ]
            ])
            ->add('user', EntityType::class, [
                "label" => "Utilisateur",
                "class" => User::class,
                "choice_label" => function($user) {
                    return $user->getFirstName()." ".strtoupper($user->getLastName());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', IntegerType::class, [

            ])
            ->add('sentAt', DateTimeType::class, [
                "label" => "Envoyé le",
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                "label" => "Status",
                "choices" => [
                    "PAYE" => "PAID",
                    "ENVOYE" => "SENT",
                    "ANNULE" => "CANCELED"
                ]
            ])
            ->add('chrono', IntegerType::class, [
                "label" => "Chrono",
                "attr" => [
                    "placeholder" => "Chrono..."
                ]
            ])
            ->add('customer', EntityType::class, [
                "label" => "Client",
                "class" => Customer::class,
                "choice_label" => function($customer) {
                    return $customer->getFirstName()." ".strtoupper($customer->getLastName());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}

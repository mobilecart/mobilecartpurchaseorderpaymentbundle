<?php

namespace MobileCart\PurchaseOrderPaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseOrderPaymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', 'text', [
                'label' => 'PO Number',
                'required' => 1,
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'po';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}

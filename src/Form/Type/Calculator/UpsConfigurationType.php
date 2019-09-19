<?php

namespace Odiseo\SyliusUpsPlugin\Form\Type\Calculator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class UpsConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('account', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.account_number',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('accesskey', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.access_key',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.username',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('password', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.password',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('origination_postcode', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.postcode',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('origination_country_code', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.country_code',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('origination_city', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.city',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
            ->add('origination_address', TextType::class, [
                'label' => 'odiseo_sylius_ups.form.calculator.address',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'odiseo_sylius_ups_shipping_calculator_ups';
    }
}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class PurchaseOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('customer', Select2EntityType::class, [
                      'multiple' => false,
                      'remote_route' => 'customer_findall',
                      'class' => '\AppBundle\Entity\Customer',
                      'primary_key' => 'id',
                      'text_property' => 'name',
                      'minimum_input_length' => 2,
                      'allow_clear' => true,
                      'cache' => true,
                      'cache_timeout' => 60000, // if 'cache' is true
                      'placeholder' => 'Nombre del cliente',
                     ])
            ->add('date')
            ->add('orderState')
            ->add('salesPoint')
            ->add('orderItems', CollectionType::class, array(
                  'entry_type'   => OrderItemType::class,
                  'allow_add' => true,
                  'allow_delete' => true,
                  'prototype' => true,
                  ))
            ->add('subtotal')
            ->add('discountAmount')
            ->add('shippingAmount')
            ->add('total');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PurchaseOrder'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_purchaseorder';
    }


}

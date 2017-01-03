<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PurchaseOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('subtotal')
            ->add('discountAmount')
            ->add('shippingAmount')
            ->add('total')
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
            'placeholder' => 'Seleccione un cliente',
        ])
            ->add('orderState')
            ->add('salesPoint');
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

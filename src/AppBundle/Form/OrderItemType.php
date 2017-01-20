<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('productCode', TextType::class, array('attr' => array('autocomplete' => 'off')))
                ->add('productQuantity', NumberType::class, array('attr' => array('autocomplete' => 'off', 'class' => 'text-right')))
                ->add('productDescription', TextType::class, array('attr' => array('autocomplete' => 'off')))
                ->add('unitPrice', MoneyType::class, array('currency' => 'USD', 'scale' => 4, 'attr' => array('autocomplete' => 'off', 'class' => 'text-right', 'onchange' => 'updateTotals(this)', 'onkeyup' => 'updateTotals(this)')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrderItem'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_orderitem';
    }


}

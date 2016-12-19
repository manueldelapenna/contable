<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class ProductDatatable
 *
 * @package AppBundle\Datatables
 */
class ProductDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->topActions->set(array(
            'start_html' => '<div class="row"><div class="col-sm-3">',
            'end_html' => '<hr></div></div>',
            'actions' => array(
                array(
                    'route' => $this->router->generate('product_new'),
                    'label' => $this->translator->trans('datatables.actions.new'),
                    'icon' => 'glyphicon glyphicon-plus',
                    'attributes' => array(
                        'rel' => 'tooltip',
                        'title' => $this->translator->trans('datatables.actions.new'),
                        'class' => 'btn btn-primary',
                        'role' => 'button'
                    ),
                )
            )
        ));

        $this->features->set(array(
            'auto_width' => true,
            'defer_render' => false,
            'info' => true,
            'jquery_ui' => false,
            'length_change' => true,
            'ordering' => true,
            'paging' => true,
            'processing' => true,
            'scroll_x' => false,
            'scroll_y' => '',
            'searching' => true,
            'state_save' => false,
            'delay' => 0,
            'extensions' => array()
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('product_results'),
            'type' => 'GET'
        ));

        $this->options->set(array(
            'display_start' => 0,
            'defer_loading' => -1,
            'dom' => 'lfrtip',
            'length_menu' => array(10, 25, 50, 100),
            'order_classes' => true,
            'order' => array(array(0, 'asc')),
            'order_multi' => true,
            'page_length' => 10,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            'renderer' => '',
            'scroll_collapse' => false,
            'search_delay' => 0,
            'state_duration' => 7200,
            'stripe_classes' => array(),
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => false,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
            'force_dom' => false
        ));

        $this->columnBuilder
            ->add('id', 'column', array(
                'title' => 'Id',
            ))
            ->add('code', 'column', array(
                'title' => 'Code',
            ))
            ->add('providerCode', 'column', array(
                'title' => 'ProviderCode',
            ))
            ->add('name', 'column', array(
                'title' => 'Name',
            ))
            ->add('description', 'column', array(
                'title' => 'Description',
            ))
            ->add('price', 'column', array(
                'title' => 'Price',
            ))
            ->add('stock', 'column', array(
                'title' => 'Stock',
            ))
            ->add('minStock', 'column', array(
                'title' => 'MinStock',
            ))
            ->add('maxStock', 'column', array(
                'title' => 'MaxStock',
            ))
            ->add('category.id', 'column', array(
                'title' => 'Category Id',
            ))
            ->add('category.name', 'column', array(
                'title' => 'Category Name',
            ))
            ->add('provider.id', 'column', array(
                'title' => 'Provider Id',
            ))
            ->add('provider.name', 'column', array(
                'title' => 'Provider Name',
            ))
            ->add('provider.email', 'column', array(
                'title' => 'Provider Email',
            ))
            ->add('provider.cuit', 'column', array(
                'title' => 'Provider Cuit',
            ))
            ->add('provider.phone', 'column', array(
                'title' => 'Provider Phone',
            ))
            ->add('provider.address', 'column', array(
                'title' => 'Provider Address',
            ))
            ->add('provider.zipcode', 'column', array(
                'title' => 'Provider Zipcode',
            ))
            ->add('provider.city', 'column', array(
                'title' => 'Provider City',
            ))
            ->add('provider.state', 'column', array(
                'title' => 'Provider State',
            ))
            ->add('provider.observations', 'column', array(
                'title' => 'Provider Observations',
            ))
            ->add('tax.id', 'column', array(
                'title' => 'Tax Id',
            ))
            ->add('tax.name', 'column', array(
                'title' => 'Tax Name',
            ))
            ->add('tax.amount', 'column', array(
                'title' => 'Tax Amount',
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'product_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'product_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.edit'),
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\Product';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_datatable';
    }
}

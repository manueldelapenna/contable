<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class UserDatatable
 *
 * @package AppBundle\Datatables
 */
class UserDatatable extends AbstractDatatableView
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
                    'route' => $this->router->generate('user_new'),
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
            'server_side' => true,
            'state_save' => false,
            'delay' => 0,
            'extensions' => array(
                                'buttons' =>array(
                                                    //'colvis',
                                                    'excel'=> array(
                                                                    'extend' => 'excel',
                                                                    'text' => 'Exportar Excel',
                                                                    'exportOptions' => array(
                                                                                            // show only the following columns:
                                                                                            'columns' => array(
                                                                                                                '0',
                                                                                                                '1', 
                                                                                                                '2', 
                                                                                                                '3', 
                                                                                                                '4', 
                                                                                                                '5', 
                                                                                                                '6', 
                                                                                                               )
                                                                                            ),
                                                                    ),
                                                ),
                                'responsive' => true
                                )
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('user_results'),
            'type' => 'GET'
        ));

        $this->options->set(array(
            'display_start' => 0,
            'defer_loading' => -1,
            'dom' => 'lfrtip',
            'length_menu' => array(50, 100, 200, -1),
            'order_classes' => true,
            'order' => array(array(0, 'asc')),
            'order_multi' => true,
            'page_length' => 50,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            'renderer' => '',
            'scroll_collapse' => false,
            'search_delay' => 1000,
            'state_duration' => 7200,
            'stripe_classes' => array(),
            'class' => Style::BOOTSTRAP_3_STYLE . ' table-condensed',
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
            'force_dom' => false
        ));

        $this->columnBuilder
            ->add('id', 'column', array(
                'title' => 'Id',
                'width' => '40px',  
            ))
            ->add('username', 'column', array(
                'title' => 'Nombre de usuario',
            	'editable' => false,
                'width' => '130px', 
            ))
            ->add('email', 'column', array(
                'title' => 'Email',
            	'editable' => false,
                'width' => '110px', 
            ))
            ->add('firstname', 'column', array(
                'title' => 'Nombre',
            	'editable' => false,
                'width' => '130px', 
            ))
            ->add('lastname', 'column', array(
                'title' => 'Apellido',
            	'editable' => false,
                'width' => '130px', 
            ))
                
            ->add('roles', 'column', array(
                'visible'=> false,
                'title' => 'Roles',
            	'editable' => false,
                'width' => '130px', 
            ))
            ->add('enabled', 'boolean', array(
                'title' => 'Habilitado',
                'true_icon' => 'glyphicon glyphicon-ok',
                'false_icon' => 'glyphicon glyphicon-remove',
                'true_label' => 'Sí',
                'false_label' => 'No',
            	'editable' => false,
                'width' => '80px', 
                'filter' => array(
                                'select', array(
                                                'search_type' => 'eq',
                                                'select_options' => array(
                                                                          '' => 'Todos', '1' => 'Sí', '0' => 'No'),
                                                )
                                   )

            ))
            
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'user_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                            'class' => 'btn btn-success btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'user_edit',
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
        return 'AppBundle\Entity\User';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_datatable';
    }
}

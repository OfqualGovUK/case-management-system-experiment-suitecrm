<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}


$module_name = 'OQ_Qualifications';
$subpanel_layout = array(
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopCreateButton'),
        array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => $module_name),
    ),

    'where' => '',

    'list_fields' => array(
        'name' => array(
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '40%',
        ),

        'number' => array(
            'vname' => 'LBL_QUALIFICATION_NUMBER',
            'width' => '6%',
        ),

        'type' => array(
            'vname' => 'LBL_TYPE',
            'width' => '10%',
        ),
        'date_entered' => array(
            'vname' => 'LBL_LIST_DATE_CREATED',
            'width' => '15%',
        ),
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'assigned_user_id',
            'target_module' => 'Employees',
        ),
        'edit_button' => array(
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Cases',
            'width' => '4%',
        ),
        'remove_button' => array(
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Cases',
            'width' => '5%',
        ),

    ),
);

<?php

$layout_defs['Accounts']['subpanel_setup']['oq_qualifications_accounts'] = array(
    'order' => 100,
    'module' => 'OQ_Qualifications',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_OQ_QUALIFICATIONS_ACCOUNTS_FROM_OQ_QUALIFICATIONS_TITLE',
    'get_subpanel_data' => 'oq_qualifications_accounts',
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopButtonQuickCreate'),
        array('widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'),
    ),
);

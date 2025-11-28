<?php

$layout_defs['Cases']['subpanel_setup']['oq_qualifications_cases'] = array(
    'order' => 100,
    'module' => 'OQ_Qualifications',
    'subpanel_name' => 'default',
    'title_key' => 'LBL_OQ_QUALIFICATIONS_CASES_FROM_OQ_QUALIFICATIONS_TITLE',
    'get_subpanel_data' => 'oq_qualifications_cases',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopButtonQuickCreate'),
        array('widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'),
    ),
);

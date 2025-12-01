<?php
$module = 'OQ_Qualifications';
$layout_defs[$module]['subpanel_setup']['securitygroups'] = [
    'top_buttons' => [
        [
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
            'popup_module' => 'SecurityGroups',
        ],
    ],
    'module' => 'SecurityGroups',
    'subpanel_name' => 'default',
    'get_subpanel_data' => 'SecurityGroups',
    'title_key' => 'LBL_SECURITYGROUPS_SUBPANEL_TITLE',
    'order' => 900,
    'sort_order' => 'asc',
    'sort_by' => 'name',
    'refresh_page' => 1,
    'add_subpanel_data' => 'securitygroup_id',
];

$layout_defs[$module]['subpanel_setup']['oq_qualifications_cases'] = [
    'top_buttons' => [
        [
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ],
    ],
    'module' => 'Cases',
    'subpanel_name' => 'default',
    'get_subpanel_data' => 'oq_qualifications_cases',
    'title_key' => 'LBL_QUALIFICATIONS_CASES_SUBPANEL_TITLE',
    'order' => 100,
    'sort_order' => 'asc',
    'sort_by' => 'name',

];

<?php
$dictionary['OQ_Qualifications'] = array(
    'table' => 'oq_qualifications',
    'audited' => true,
    'inline_edit' => true,
    'duplicate_merge' => true,
    'fields' => array(
        'name' =>
        array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
            'full_text_search' =>
            array(
                'enabled' => true,
                'boost' => 3,
            ),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'number' =>
        array(
            'name' => 'number',
            'vname' => 'LBL_QUALIFICATION_NUMBER',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
            'full_text_search' =>
            array(
                'enabled' => true,
                'boost' => 3,
            ),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'level' =>
        array(
            'name' => 'level',
            'vname' => 'LBL_QUALIFICATION_LEVEL',
            'type' => 'enum',
            'options' => 'qualification_level_list',
            'len' => 100,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'sub_level' =>
        array(
            'name' => 'sub_level',
            'vname' => 'LBL_QUALIFICATION_SUB_LEVEL',
            'type' => 'enum',
            'options' => 'qualification_sublevel_list',
            'len' => 100,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'eqf_level' =>
        array(
            'name' => 'eqf_level',
            'vname' => 'LBL_EQF_LEVEL',
            'type' => 'enum',
            'options' => 'eqf_level_list',
            'len' => 100,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'type' =>
        array(
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'qualification_type_list',
            'len' => 100,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'total_credits' =>
        array(
            'name' => 'total_credits',
            'vname' => 'LBL_TOTAL_CREDITS',
            'type' => 'int',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'regulation_start_date' =>
        array(
            'name' => 'regulation_start_date',
            'vname' => 'LBL_REGULATION_START_DATE',
            'type' => 'datetime',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'operational_start_date' =>
        array(
            'name' => 'operational_start_date',
            'vname' => 'LBL_OPERATIONAL_START_DATE',
            'type' => 'datetime',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),
        'operational_end_date' =>
        array(
            'name' => 'operational_end_date',
            'vname' => 'LBL_OPERATIONAL_END_DATE',
            'type' => 'datetime',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
        ),

        // ... additional field definitions ...
        'oq_qualifications_cases' =>
        array(
            'name' => 'oq_qualifications_cases',
            'vname' => 'LBL_OQ_QUALIFICATIONS_CASES_FROM_CASES_TITLE',
            'type' => 'link',
            'relationship' => 'oq_qualifications_cases',
            'module' => 'Cases',
            'bean_name' => 'Case',
            'source' => 'non-db',
        ),

        'oq_qualifications_accounts' => array(
            'name' => 'oq_qualifications_accounts',
            'type' => 'link',
            'relationship' => 'oq_qualifications_accounts',
            'source' => 'non-db',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'vname' => 'LBL_OQ_QUALIFICATIONS_ACCOUNTS_FROM_OQ_QUALIFICATIONS_TITLE',
            'id_name' => 'oq_qualifications_accountsoq_qualifications_ida',
        ),
        'oq_qualifications_accounts_name' => array(
            'name' => 'oq_qualifications_accounts_name',
            'rname' => 'name',
            'id_name' => 'oq_qualifications_accountsoq_qualifications_ida',
            'vname' => 'LBL_OQ_QUALIFICATIONS_ACCOUNTS_FROM_OQ_QUALIFICATIONS_TITLE',
            'type' => 'relate',
            'link' => 'oq_qualifications_accounts',
            'table' => 'accounts',
            'module' => 'Accounts',
            'source' => 'non-db',
            'save' => true,
        ),
        'oq_qualifications_accountsoq_qualifications_ida' => array(
            'name' => 'oq_qualifications_accountsoq_qualifications_ida',
            'type' => 'link',
            'relationship' => 'oq_qualifications_accounts',
            'source' => 'non-db',
            'side' => 'right',
            'reportable' => false,
            'vname' => 'LBL_OQ_QUALIFICATIONS_ACCOUNTS_FROM_OQ_QUALIFICATIONS_TITLE_ID',

        ),
    ),
    'relationships' => array(),
    'optimistic_locking' => true,
    'unified_search' => true,

);
if (!class_exists('VardefManager')) {
    require_once 'include/SugarObjects/VardefManager.php';
}

VardefManager::createVardef('OQ_Qualifications', 'OQ_Qualifications', array('basic', 'assignable', 'security_groups'));

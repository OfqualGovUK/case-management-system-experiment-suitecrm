<?php
$dictionary['oq_qualifications_accounts'] = array(
    'true_relationship_type' => 'one-to-many',
    'relationships' => array(
        'oq_qualifications_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'OQ_Qualifications',
            'rhs_table' => 'oq_qualifications',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'oq_qualifications_accounts_c',
            'join_key_rhs' => 'oq_qualifications_accountsoq_qualifications_ida',
            'join_key_lhs' => 'oq_qualifications_accountsaccounts_idb',
        ),
    ),
    'table' => 'oq_qualifications_accounts_c',
    'fields' => array(
        0 => [
            'name' => 'id',
            'type' => 'varchar',
            'len' => 36,
        ],
        1 => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        2 => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => true,
        ],
        3 => [
            'name' => 'oq_qualifications_accountsoq_qualifications_ida',
            'type' => 'varchar',
            'len' => 36,
        ],
        4 => [
            'name' => 'oq_qualifications_accountsaccounts_idb',
            'type' => 'varchar',
            'len' => 36,
        ],
    ),
    'indices' => array(
        0 => array(
            'name' => 'oq_qualifications_accountsspk',
            'type' => 'primary',
            'fields' => array(
                0 => 'id',
            ),
        ),
        1 => array(
            'name' => 'oq_qualifications_accounts_ida1',
            'type' => 'alternate_key',
            'fields' => array(
                0 => 'oq_qualifications_accountsoq_qualifications_ida',
            ),
        ),
        2 => array(
            'name' => 'oq_qualifications_accounts_idb2',
            'type' => 'index',
            'fields' => array(
                0 => 'oq_qualifications_accountsaccounts_idb',
            ),
        ),
    ),
);

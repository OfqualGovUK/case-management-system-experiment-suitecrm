<?php
// define the relationship metadata for Qualifications and Accounts
// the name of the relationship is oq_qualifications_cases
$dictionary['oq_qualifications_cases'] = array(
    'true_relationship_type' => 'many-to-many', // enforces many-to-many relationship
    'relationships' => array(
        'oq_qualifications_cases' => array(
            'lhs_module' => 'OQ_Qualifications',
            'lhs_table' => 'oq_qualifications',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'oq_qualifications_cases_c',
            'join_key_lhs' => 'oq_qualifications_casesoq_qualifications_ida',
            'join_key_rhs' => 'oq_qualifications_casescases_idb',
        ),
    ),
    'table' => 'oq_qualifications_cases_c', //the actual table used to store the relationship
    'fields' => array( //the fields that make up the table
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
            'name' => 'oq_qualifications_casesoq_qualifications_ida',
            'type' => 'varchar',
            'len' => 36,
        ],
        4 => [
            'name' => 'oq_qualifications_casescases_idb',
            'type' => 'varchar',
            'len' => 36,
        ],
    ),
    'indices' => array( //the indices for the table
        0 => array(
            'name' => 'oq_qualifications_casesspk',
            'type' => 'primary',
            'fields' => array(
                0 => 'id',
            ),
        ),
        1 => array(
            'name' => 'oq_qualifications_cases_ida1',
            'type' => 'alternate_key',
            'fields' => array(
                0 => 'oq_qualifications_casesoq_qualifications_ida',
                1 => 'oq_qualifications_casescases_idb',
            ),
        ),

    )
);

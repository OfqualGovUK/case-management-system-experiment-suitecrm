<?php
//define the link field in the Cases module to relate to Qualifications
$dictionary['Case']['fields']['oq_qualifications_cases'] = array(
    'name' => 'oq_qualifications_cases',
    'type' => 'link',
    'relationship' => 'oq_qualifications_cases',
    'source' => 'non-db',
    'module' => 'OQ_Qualifications',
    'bean_name' => 'OQ_Qualifications',
    'vname' => 'LBL_OQ_QUALIFICATIONS_CASES_FROM_OQ_QUALIFICATIONS_TITLE',
);

<?php
//define the link field in the Accounts module to relate to Qualifications
$dictionary['Account']['fields']['oq_qualifications_accounts'] =
    array(
        'name' => 'oq_qualifications_accounts',
        'vname' => 'LBL_OQ_QUALIFICATIONS_ACCOUNTS_FROM_ACCOUNTS_TITLE',
        'type' => 'link',
        'relationship' => 'oq_qualifications_accounts',
        'module' => 'OQ_Qualifications',
        'bean_name' => 'OQ_Qualifications',
        'side' => 'right',
        'source' => 'non-db',
    );

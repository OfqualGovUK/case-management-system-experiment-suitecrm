<?php

/**
 * This Logic hook sets all fields of AUDITED modules to be audited
 * update dictionary[<modulename}]['audited']=true to set individual modules to audit
 * 
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class All_Audit
{

    public function updateAudit($bean, $event, $arguments)
    {
        global $db;

        if (!empty($bean->fetched_row) && $bean->is_AuditEnabled()) {
            $changed_fields = [];

            foreach ($bean->field_defs as $field_name => $field_def) {
                if (
                    isset($bean->$field_name) &&
                    (!array_key_exists($field_name, $bean->fetched_row) ||
                        $bean->fetched_row[$field_name] !== $bean->$field_name)
                ) {
                    $changed_fields[$field_name] = $field_def;
                }
            }
            $bean->audit_enabled_fields = [];
            $auditDataChanges = $db->getDataChanges($bean, array_keys($changed_fields));
            if (!empty($auditDataChanges)) {
                foreach ($auditDataChanges as $field => $change) {
                    $db->save_audit_records($bean, $change);
                    $bean->fetched_row[$change['field_name']] = $change['after'];
                }
                $bean->createdAuditRecords = true;
            }
        }
    }
}

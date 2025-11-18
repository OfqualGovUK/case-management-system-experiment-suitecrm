<?php

$hook_array['after_save'][] = array(
    1,
    'Send Notification on Case Save',
    'custom/modules/Cases/LogicHooks/SendNotification.php',
    'CaseNotificationHook',
    'sendCaseNotification'
);

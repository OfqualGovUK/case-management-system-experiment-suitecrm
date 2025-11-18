<?php

/**
 * This Logic hook sends a notification on Case save
 * 
 * 
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once $GLOBALS['BASE_DIR'] . '/custom/Services/OfqualNotifications.php';


class CaseNotificationHook
{

    public function sendCaseNotification($bean, $event, $arguments)
    {
        global $db;

        $userid = $bean->modified_user_id;

        if (empty($bean->fetched_row)) {
            $summary = "A new case record has been created with case number: " . $bean->case_number . " title: " . $bean->name;
        } else {
            $summary = "The case record with case number: " . $bean->case_number . " has been updated.";
        }

        $User = \BeanFactory::getBean('Users', $userid);
        $useremail = $User->email1;
        if (empty($useremail) || !filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
            \LoggerManager::getLogger()->error("Failed to send notification to user: {$User->user_name} with ID: {$userid}, invalid email address.");
            return true;
        }

        try {

            $notify = new OfqualNotifications();
            $res = $notify->sendNotification($summary, $useremail);
            if ($res === false) {
                \LoggerManager::getLogger()->error("Failed to send notification for Case {$bean->case_number} to user email: {$useremail}");
            }
            return true;
        } catch (Exception $e) {
            \LoggerManager::getLogger()->error("Failed to send notification for Case {$bean->case_number}: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            \LoggerManager::getLogger()->error("Error: " . $e->getMessage());
        }
    }
}

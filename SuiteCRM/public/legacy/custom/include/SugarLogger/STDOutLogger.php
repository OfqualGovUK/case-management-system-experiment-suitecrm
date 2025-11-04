<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/SugarLogger/LoggerManager.php');
require_once('include/SugarLogger/LoggerTemplate.php');

/**
 * Ofqual Default SugarCRM Logger
 * @api
 */
#[\AllowDynamicProperties]
class STDOutLogger implements LoggerTemplate
{

    /**
     * Let's us know if we've initialized the logger file
     */
    protected $initialized = false;

    /**
     * Logger file handle
     */
    protected $fp = false;

    public function __get(
        $key
    ) {
        return $this->$key;
    }

    /**
     * Constructor
     *
     * Reads the config file for logger settings
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $config = SugarConfig::getInstance();

        $this->_doInitialization();
        LoggerManager::setLogger('default', 'STDOutLogger');
    }

    /**
     * Handles the SugarLogger initialization
     */
    protected function _doInitialization()
    {

        $this->initialized = true;

    }

    /**
     * for log() function, it shows a backtrace information in log when
     * the 'show_log_trace' config variable is set and true
     * @return string a readable trace string
     */
    private function getTraceString()
    {
        $ret = '';
        $trace = debug_backtrace();
        foreach ($trace as $call) {
            $file = isset($call['file']) ? $call['file'] : '???';
            $line = isset($call['line']) ? $call['line'] : '???';
            $class = isset($call['class']) ? $call['class'] : '';
            $type = isset($call['type']) ? $call['type'] : '';
            $function = isset($call['function']) ? $call['function'] : '???';
            $ret .= "\nCall in {$file} at #{$line} from {$class}{$type}{$function}(...)";
        }
        $ret .= "\n";
        return $ret;
    }

    /**
     * Show log
     * and show a backtrace information in log when
     * the 'show_log_trace' config variable is set and true
     * see LoggerTemplate::log()
     */
    public function log(
        $level,
        $message
    ) {
        global $sugar_config, $timezone;

        if (!$this->initialized) {
            return;
        }
        //lets get the current user id or default to -none- if it is not set yet
        $userID = (!empty($GLOBALS['current_user']->id)) ? $GLOBALS['current_user']->id : '-none-';

        if (!$this->fp) {
            $this->fp = fopen('php://stdout', 'w');

            // Check if fopen failed and provide clear error message
            if ($this->fp === false) {
                // Try to provide a helpful error message
                $error_msg = "SugarLogger: Unable to open log file '{$this->full_log_file}' for writing. ";

                if (!file_exists(dirname($this->full_log_file))) {
                    $error_msg .= "Directory '" . dirname($this->full_log_file) . "' does not exist.";
                } elseif (!is_writable(dirname($this->full_log_file))) {
                    $error_msg .= "Directory '" . dirname($this->full_log_file) . "' is not writable.";
                } elseif (file_exists($this->full_log_file) && 
                            !is_writable($this->full_log_file)) {
                    $error_msg .= "Log file exists but is not writable.";
                } else {
                    $error_msg .= "Check file permissions and disk space.";
                }

                // Output error to stderr so it doesn't corrupt web responses
                error_log($error_msg);
            }
        }

        // change to a string if there is just one entry
        if (is_array($message) && count($message) == 1) {
            $message = array_shift($message);
        }
        // change to a human-readable array output if it's any other array
        if (is_array($message)) {
            $message = print_r($message, true);
        }

        if (isset($sugar_config['show_log_trace']) && $sugar_config['show_log_trace']) {
            $trace = $this->getTraceString();
            $message .= ("\n" . $trace);
        }

        $language = $sugar_config['default_language'];

        $format = new IntlDateFormatter(
            $language,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::MEDIUM,
            $timezone,
            IntlDateFormatter::GREGORIAN,
            $this->getDateFormatString(),
        );

        //write out to the file including the time in the dateFormat the process id , the user id , 
        // and the log level as well as the message
        if (is_resource($this->fp)) {
            fwrite(
                $this->fp,
                $this->formatLog($format, $userID, $level, $message)
            );
        }
    }

    /**
     * This is needed to prevent unserialize vulnerability
     */
    public function __wakeup()
    {
        // clean all properties
        foreach (get_object_vars($this) as $k => $v) {
            $this->$k = null;
        }
        throw new Exception("Not a serializable object");
    }

    /**
     * Destructor
     *
     * Closes the SugarLogger file handle
     */
    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
            $this->fp = false;
        }
    }

    /**
     * @return string
     */
    protected function getDateFormatString(): string
    {
        return "EEE MMM d yyyy 'at' HH:mm:ss";
    }

    /**
     * @param IntlDateFormatter $format
     * @param string $userID
     * @param $level
     * @param mixed $message
     * @return string
     */
    protected function formatLog(IntlDateFormatter $format, string $userID, $level, mixed $message): string
    {
        return $format->format(time()) . ' [' . getmypid() . 
                        '][' . $userID . '][' . strtoupper($level) . '] ' . $message . "\n";
    }
}

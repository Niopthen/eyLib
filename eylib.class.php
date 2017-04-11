<?php

/*
 *
 * ===============================================
 * Name: eylib.class.php
 * ===============================================
 * Descriptions:
 * Global eyLib Bibliothek Class
 *
 * ===============================================
 */
require_once './eylib.config.php';

class eyLib
{

    public $ERROR_EMAIL_ADDRESS = 'name@localhost';
    public $LOG_FOLDER;

    /**
     *  Default Loader Function of the eylib Class
     */
    function __construct()
    {
        $handle = opendir(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "internals" . DIRECTORY_SEPARATOR);
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                if ((substr($file, count($file) - 4)) == "php")
                {
                    require_once(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "internals" . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        $this->global_constants();
    }

    /**
     * Output of Error Information (only HTML PAGE or PAGE WITH JS)
     * @param string $Message
     * @param bool $Display_as_JS 
     */
    public function error_handler($Message, $Display_as_JS = FALSE)
    {
        $OutputMessage = empty($Message) ? "General System Error" : $Message;

        if ($Display_as_JS == TRUE)
        {
            echo "<html><head><title>SystemError</title><body><script>alert('$OutputMessage');</script></body></html>";
        }
        else
        {
            echo "<html><head><title>SystemError</title><body>$OutputMessage</body></html>";
        }
    }

    /**
     *
     * @param array $data
     * @return object
     *
     * wandle ein Array in ein object um
     */
    public function array2obj($data)
    {
        return is_array($data) ? (object) array_map(__FUNCTION__, $data) : $data;
    }

    /**
     * Definition of global constants
     */
    private function global_constants()
    {
        defined("IP_ADDRESS")
                or define("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
    }

    /**
     * simple email function to send information to the defined email address
     * @param string $content 
     */
    public function send_content($content)
    {
        mail($this->ERROR_EMAIL_ADDRESS, 'EYLIB SendContent', $content);
    }

    /**
     *
     * write log file to the configured folder
     * 
     * @param string $content
     * @param bool $override 
     */
    public function write_log($content, $override = FALSE)
    {

        if (defined(EYLIB_LOG_PATH))
        {
            $log_folder = EYLIB_LOG_PATH;
        }
        else
        {

            $log_folder = $this->LOG_FOLDER == '' ? $_SERVER['DOCUMENT_ROOT'] . ".." . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR : $this->LOG_FOLDER;
        }

        if (is_dir($log_folder))
        {
            $fp = '';
            $content .= "\n";
            $logfile_path_name = $log_folder . 'eylib_log-' . date('y.d.m', time()) . '.log';

            if (file_exists($logfile_path_name != TRUE))
            {
                $fp = fopen($logfile_path_name, 'w');
            }
            else
            {
                if ($override == TRUE)
                {
                    $fp = fopen($logfile_path_name, 'r+');
                }
                else
                {
                    $fp = fopen($logfile_path_name, 'a');
                }
            }
            fputs($fp, $content);
            fclose($fp);
        }
        else
        {
            $this->error_handler(ERR_EYLIB_LOG_PATH, TRUE);
        }
    }

    /**
     * search if this is an simple or multidimensional array
     * @param array $array
     * @return boolean 
     */
    public function is_single_array($array)
    {
        $retVal = TRUE;
        if (is_array($array))
        {

            foreach (array_keys($array) as $value)
            {
                if (is_array($array[$value]))
                {
                    $retVal = FALSE;
                }
            }
        }
        return $retVal;
    }

    /**
     * check the array dimension
     * 
     * @param array $array
     * @return string
     */
    public function array_dimension($array)
    {
        $retVal = 0;
        if (is_array($array))
        {
            $retVal = $this->array_dimension_check($array);
        }
        return $retVal;
    }

    /**
     * check the array dimension
     * 
     * @param array $array
     * @param string $dimension
     * @return string
     */
    private function array_dimension_check($array, $dimension = 1)
    {
        $keys = array_keys($array);
        foreach ($keys as $value)
        {
            if (is_array($array[$value]))
            {
                $dimension = $dimension + 1;
                $this->array_dimension_check($array[$value], $dimension);
            }
        }

        return $dimension;
    }

}
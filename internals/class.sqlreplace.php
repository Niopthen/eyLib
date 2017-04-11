<?php

/**
 * Replace Values in SQL String
 *
 */
class eylib_sqlreplace extends eylib
{

    var $_leftdelimiter = '<[';
    var $_rightdelimiter = ']>';
    var $_query;

    function __construct($leftdelimiter = '', $rightdelimiter = '')
    {
        if ($leftdelimiter != $rightdelimiter)
        {
            if ($leftdelimiter != '')
            {
                $this->_leftdelimiter = $leftdelimiter;
            }

            if ($rightdelimiter != '')
            {
                $this->_rightdelimiter = $rightdelimiter;
            }
        }
    }

    function replace($query, $replace, $replace_value)
    {
        $_replace = $this->_leftdelimiter . $replace . $this->_rightdelimiter;
        return str_replace($_replace, $replace_value, $query);
    }

    function array_replace($replace, $replace_value, $query)
    {
        if ((count($replace)) == (count($replace_value)))
        {
            $replace = $this->check_delimiter($replace);
            return str_replace($replace, $replace_value, $query);
        }
        else
        {
            return false;
        }
    }

    private function check_delimiter($array)
    {
        foreach ($array as $value)
        {
            if ((substr($value, 0, 1) != $this->_leftdelimiter) && (substr($value, strlen($value), 1) != $this->_rightdelimiter))
            {
                $_newArray[] = $this->_leftdelimiter . $value . $this->_rightdelimiter;
            }
            else
            {
                $_newArray[] = $value;
            }
        }
        return $_newArray;
    }

}
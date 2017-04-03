<?php

/*
 *
 * ===============================================
 * Name: class.db.mysqli.php
 * ===============================================
 *
 * Descriptions:
 * Global eyLib Bibliothek Class
 *
 * ===============================================
 */

class eylib_mysqli extends eylib
{

    public $name_fields;
    public $num_rows;
    public $num_fields;
    public $assoc_array;
    public $affected_rows;
    public $fields;
    public $getFirstRow = FALSE;
    public $getLastRow = FALSE;
    public $getValue = FALSE;
    public $getValues = FALSE;
    private $DB_Handle;
    private $DB_connection_data;
    private $LogMultiple = FALSE;
    private $LogFileName = 'eyMySQL';

    /**
     * Connection to the database
     * @param string $DB_IDENTIFIER_NAME
     * @param array $DB_Array
     */
    function __construct($DB_IDENTIFIER_NAME, $DB_Array = '')
    {
        if (($DB_Array != '') && (is_array($DB_Array)))
        {
            // DB Verbindung ueber ein uebergebenes Array
        }
        elseif ((is_array($GLOBALS['DB_MYSQL'])) && (array_key_exists($DB_IDENTIFIER_NAME, $GLOBALS['DB_MYSQL'])))
        {
            if (is_array($GLOBALS['DB_MYSQL']["$DB_IDENTIFIER_NAME"]))
            {
                $this->DB_connection_data = $GLOBALS['DB_MYSQL']["$DB_IDENTIFIER_NAME"];
            }
            else
            {
                $this->error_handler('DB Verbindungsdaten ist kein Array', TRUE);
                die();
            }
        }
        else
        {
            $this->error_handler("Unbekannter DB Identifier: $DB_IDENTIFIER_NAME", TRUE);
            die();
        }
        parent::__construct();
    }
    /**
     * Connect to the database
     */
    private function DB_Connect()
    {
        $dbh = mysqli_init();
        if (!$dbh)
        {
            $this->error_handler('keine Verbindung moeglich', TRUE);
        }

        mysqli_real_connect(
                $dbh, $this->DB_connection_data['DB_SERVER_IP'], $this->DB_connection_data['DB_USER_NAME'], $this->DB_connection_data['DB_USER_PASSWORD'], $this->DB_connection_data['DB_NAME'], $this->DB_connection_data['DB_PORT'], NULL, MYSQLI_CLIENT_COMPRESS
        );
        mysqli_set_charset($dbh, $this->DB_connection_data['DB_CHARSET']);
        $this->DB_Handle = $dbh;
    }
    public function DB_MSelect($query_array, $SQL_NAME = '')
    {
        $query = '';
        $ErgBezeichner = '';
        $retVal = FALSE;
        $result = '';
        $index = 0;

        if (!is_array($query_array))
        {
            $this->mySQL_Log('MultiSelect Select is no array');
            die();
        }

        /* Build $query */
        foreach ($query_array as $key => $value)
        {
            $value = trim($value);
            if (substr($value, -1) != ';')
            {
                $value = $value . ';';
            }
            $query .= $value;
            $ErgBezeichner[] = $key;
        }


        $this->DB_Connect();

        /* execute multi query */
        if (mysqli_multi_query($this->DB_Handle, $query))
        {
            do
            {
                /* Store First Result Set */
                if ($result = mysqli_store_result($this->DB_Handle))
                {

                    while ($row = mysqli_fetch_assoc($result))
                    {
                        $ereg[$ErgBezeichner[$index]][] = $row;
                    }

                    if (count($ereg[$ErgBezeichner[$index]]) == 0)
                    {
                        $ereg[$ErgBezeichner[$index]] = array();
                    }
                    mysqli_free_result($result);
                }

                /* Nexte SQL Abfrage */
                if (mysqli_more_results($this->DB_Handle))
                {
                    $index++;
                }
            }
            while (@mysqli_next_result($this->DB_Handle));
        }
        return $ereg;
    }
    /**
     * make a select to the database
     * @param string $query
     */
    public function DB_Select($query, $SQL_NAME = '')
    {
        $doLogging = FALSE;
        if ((key_exists("$SQL_NAME", $GLOBALS['DEBUG']['SQL'])) && ($GLOBALS['DEBUG']['SQL']["$SQL_NAME"] == TRUE))
        {
            $this->mySQLTransLog('START SQL', $query, $SQL_NAME, TRUE);
            $doLogging = TRUE;
        }

        $retVal = array();
        $this->DB_Connect();
        unset($this->assoc_array);

        $sql = mysqli_query($this->DB_Handle, $query);

        if (mysqli_error($this->DB_Handle) == '')
        {

            $this->num_fields = mysqli_num_fields($sql);
            $this->num_rows = mysqli_num_rows($sql);
            $this->affected_rows = $this->num_rows; // affected rows == num_rows
            while ($temp = mysqli_fetch_assoc($sql))
            {
                $this->assoc_array[] = $temp;
            }

            if ($this->num_rows >= 1)
            {
                $retVal = $this->assoc_array;
                $this->getFirstRow = $retVal[0];

                $tmp_last_num = $this->num_rows > 1 ? $this->num_rows - 1 : 0;
                $this->getLastRow = $this->assoc_array["$tmp_last_num"];

                foreach ($this->assoc_array[0] as $key => $value)
                {
                    $this->fields[] = $key;
                }
            }
            mysqli_free_result($sql);
            mysqli_close($this->DB_Handle);

            if ($doLogging == TRUE)
            {
                $this->mySQLTransLog('END SQL', $query, $SQL_NAME);
            }

            return $retVal;
        }
        else
        {
            $this->mySQL_Log(mysqli_error($this->DB_Handle));
        }
    }
    public function DB_Insert($query, $SQL_NAME = '')
    {
        $doLogging = FALSE;
        if ((key_exists("$SQL_NAME", $GLOBALS['DEBUG']['SQL'])) && ($GLOBALS['DEBUG']['SQL']["$SQL_NAME"] == TRUE))
        {
            $this->mySQLTransLog('START SQL', $query, $SQL_NAME, TRUE);
            $doLogging = TRUE;
        }

        $this->DB_Connect();
        mysqli_query($this->DB_Handle, $query);

        if ($doLogging == TRUE)
        {
            $this->mySQLTransLog('END SQL', $query, $SQL_NAME);
        }

        if (mysqli_error($this->DB_Handle) != "")
        {
            mysqli_close($this->DB_Handle);
            die($this->error_handler(mysqli_error()));
        }
        else
        {
            $this->affected_rows = mysqli_affected_rows($this->DB_Handle);
            mysqli_close($this->DB_Handle);
        }
    }
    public function DB_Update($query, $SQL_NAME = '')
    {
        $this->DB_Insert($query);
    }
    public function DB_Update($query, $SQL_NAME = '')
    {
        $this->DB_Insert($query);
    }
    public function DB_Delete($query, $SQL_NAME = '')
    {
        $this->DB_Insert($query);
    }
    private function mySQLTransLog($content = '', $query = '', $query_name = '', $start = FALSE)
    {
        $myContent = microtime(TRUE) . ' ' . $content . " - " . $query_name;
        $myContent = $start == TRUE ? $myContent : $myContent . "\n";

        $LogMultiple = !defined('LOG_MULTIPLE') ? $this->LogMultiple : LOG_MULTIPLE;
        if (($LogMultiple == TRUE) && ($query_name == ''))
        {
            return FALSE;
        }

        if (!defined('LOG_PATH'))
        {
            return FALSE;
        }
        else
        {
            $fp = '';
            if ($LogMultiple == TRUE)
            {
                $this->write_file($myContent, $query_name);
                if ($query != '')
                {
                    $this->write_file($query . "\n\n" . "------------ 8< ------------" . "\n", $query_name . '.QUERY');
                }
            }
            else
            {
                $this->write_file($myContent);
                if ($query != '')
                {
                    $this->write_file($query_name . "\n\n" . "------------ 8< ------------" . "\n" . $query);
                }
            }
        }
    }
    private function write_file($content, $FileName = '')
    {
        $fp = '';
        $content .= "\n";
        $FileName = $FileName == '' ? date('y.m.d', time()) . '-' . $this->LogFileName . '.log' : date('y.m.d', time()) . '-' . $FileName . '.log';
        $completeFileName = LOG_PATH . $FileName;

        if (file_exists($completeFileName) != TRUE)
        {
            $fp = fopen($completeFileName, 'w');
        }
        else
        {
            $fp = fopen($completeFileName, 'a');
        }
        fputs($fp, $content);
        fclose($fp);
    }
    private function mySQL_Log($data)
    {
        $this->write_log(date('y.m.d - H:i:s', time()) . ' keine Verbindung moeglich' . $data);
    }
}
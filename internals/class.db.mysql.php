<?php

/*
 *
 * ===============================================
 * Name: class.db.mysql.php
 * ===============================================
 * Description:
 * Class for simple mySQL usage
 *
 * ===============================================
 *
 */

class eyLib_mysql extends eyLib
{

    public $name_fields;
    public $num_rows;
    public $getFirstRow = FALSE;
    public $getLastRow = FALSE;
    public $getValue = FALSE;
    public $getValues = FALSE;
    private $DB_Handle;
    private $DB_connection_data;

    /**
     *
     * Get connection to the database
     *
     * @param string $DB_IDENTIFIER_NAME
     * @param array $DB_Array
     */
    function __construct($DB_IDENTIFIER_NAME, $DB_Array = '')
    {
        if (($DB_Array != '') && (is_array($DB_Array)))
        {
            
        }
        elseif ((is_array($GLOBALS['DB_MYSQL'])) && (array_key_exists($DB_IDENTIFIER_NAME, $GLOBALS['DB_MYSQL'])))
        {
            if (is_array($GLOBALS['DB_MYSQL']["$DB_IDENTIFIER_NAME"]))
            {
                $this->DB_connection_data = $GLOBALS['DB_MYSQL']["$DB_IDENTIFIER_NAME"];
            }
            else
            {
                $this->error_handler('DB Connection Data is not an Array', TRUE);
                die();
            }
        }
        else
        {
            $this->error_handler("Unknown DB Identifier: $DB_IDENTIFIER_NAME", TRUE);
            die();
        }
        parent::__construct();
    }
    /**
     * Connect to the database
     */
    private function DB_Connect()
    {
        switch ($this->DB_connection_data['DB_CONNECTION_TYP'])
        {
            case 'connect':
                $this->DB_Handle = @mysql_connect(
                                $this->DB_connection_data['DB_SERVER_IP'] . ":" . $this->DB_connection_data['DB_PORT'], $this->DB_connection_data['DB_USER_NAME'], $this->DB_connection_data['DB_USER_PASSWORD'], false, MYSQL_CLIENT_COMPRESS
                );
                mysql_select_db($this->DB_connection_data['DB_NAME'], $this->DB_Handle);
                mysql_set_charset($this->DB_connection_data['DB_CHARSET'], $this->DB_Handle);
                break;

            case 'pconnect':
                $this->DB_Handle = mysql_pconnect(
                                $this->DB_connection_data['DB_SERVER_IP'] . ":" . $this->DB_connection_data['DB_PORT'], $this->DB_connection_data['DB_USER_NAME'], $this->DB_connection_data['DB_USER_PASSWORD'], MYSQL_CLIENT_COMPRESS
                );
                mysql_select_db($this->DB_connection_data['DB_NAME'], $this->DB_Handle);
                mysql_set_charset($this->DB_connection_data['DB_CHARSET'], $this->DB_Handle);
                break;


            default:
                die($this->error_handler("Fehlerhafter Verbindungstyp : " . $this->DB_connection_data['DB_CONNECTION_TYP'], TRUE));
        }

        if (mysql_error() != '')
        {
            $this->mySQL_Log(mysql_error());
            die($this->error_handler('keine Verbindung moeglich', TRUE));
        }
    }
    /**
     * make a select to the database
     * @param string $query
     */
    public function DB_Select($query)
    {
        $this->DB_Connect();
        $sql = (mysql_query($query));
        $result = array();

        if (mysql_error() == '')
        {

            $this->num_fields = mysql_num_fields($sql);
            $this->num_rows = mysql_num_rows($sql);

            //  get field names and return it as array
            for ($i = 0; $i < $this->num_fields; $i++)
            {
                $this->name_fields[$i] = mysql_field_name($sql, $i);
            }

            if (($this->num_fields <= 1) && ($this->num_rows <= 1))
            {
                $sql_value = mysql_fetch_row($sql);
                $fname = mysql_field_name($sql, 0);
                $fvalue = $sql_value[0];
                $result[0][$fname] = $fvalue;
            }
            elseif (($this->num_fields <= 1 ) && ($this->num_rows) > 1)
            {
                $i = 0;
                $fname = mysql_field_name($sql, 0);
                while ($sql_value = mysql_fetch_row($sql))
                {
                    $result[$i][$fname] = $sql_value[0];
                    $i++;
                }
            }
            elseif (($this->num_fields > 1 ) && ($this->num_rows) <= 1)
            {
                for ($y = 0; $y < $this->num_fields; $y++)
                {
                    $fname[$y] = mysql_field_name($sql, $y);
                }

                while ($sql_value = mysql_fetch_row($sql))
                {
                    for ($j = 0; $j < count($fname); $j++)
                    {
                        $result[0][$fname[$j]] = $sql_value[$j];
                    }
                }
            }
            elseif (($this->num_fields > 1 ) && ($this->num_rows) > 1)
            {
                $i = 0;
                for ($y = 0; $y < $this->num_fields; $y++)
                {
                    $fname[$y] = mysql_field_name($sql, $y);
                }

                while ($sql_value = mysql_fetch_row($sql))
                {
                    for ($j = 0; $j < count($fname); $j++)
                    {
                        $result[$i][$fname[$j]] = $sql_value[$j];
                    }
                    $i++;
                }
            }

            if ($this->num_rows == 1)
            {
                $this->getFirstRow = $result[0];
                $this->getLastRow = $result[0];

                if (count($result[0] == 1) && count($result == 1))
                {
                    $arr_values = array_values($result[0]);
                    $this->getValues = $arr_values;
                    $this->getValue = $arr_values[0];
                }
            }
            elseif ($this->num_rows >= 2)
            {
                $this->getFirstRow = $result[0];
                $index = $this->num_rows - 1;
                $this->getLastRow = $result["$index"];
            }

            mysql_free_result($sql);
            $this->DB_Close();

            return $result;
        }
        else
        {
            $this->mySQL_Log(mysql_error());
        }
    }
    public function DB_Insert($query)
    {
        $this->DB_Connect();
        mysql_query($query);
        if (mysql_error() != "")
        {
            $this->DB_Close();
            die($this->error_handler(mysql_error()));
        }
        else
        {
            $this->DB_Close();
        }
    }
    public function DB_Update($query)
    {
        $this->DB_Insert($query);
    }
    public function DB_Delete($query)
    {
        $this->DB_Insert($query);
    }
    private function DB_Close()
    {
        if ($this->DB_connection_data['DB_CONNECTION_TYP'] != 'pconnect')
        {
            mysql_close();
        }
    }
    private function mySQL_Log($data)
    {
        $this->write_log(date('y.m.d - H:i:s', time()) . ' keine Verbindung möglich' . $data);
    }
}
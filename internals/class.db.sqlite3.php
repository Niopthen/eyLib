<?php

/*
 *
 * ===============================================
 * Name: class.db.sqlite3.php
 * ===============================================
 * Description:
 * Database Class for SQLite 3
 *
 * ===============================================
 *
 * $Niopthen
 */

class eylib_sqlite3 extends eylib
{

    private $DB;
    public $name_fields = false;
    public $num_rows = null;
    public $getFirstRow = false;
    public $getLastRow = false;

    function __construct($DB_Name)
    {
        if (!is_string($DB_Name))
        {
            die('Error: DB Name incorrect');
        }

        try
        {
            $this->DB = new SQLite3("$DB_Name");
        } catch (Exception $e)
        {
            echo "Error [$DB_Name] -> " . $e->getMessage(), "\n";
            die();
        }
    }

    function SQLite3_Select($query)
    {
        $result = false;
        if (is_string($query))
        {
            $sql = $this->DB->query($query);

            if (!$sql)
            {
                throw new Exception(die("Error [Query]"));
            }
            else
            {
                $cols = $sql->numColumns();
                for ($i = 1; $i < $cols; $i++)
                {
                    $columns[$i] = $sql->columnName($i);
                }
                $this->name_fields = $columns;

                while ($row = $sql->fetchArray())
                {
                    foreach ($columns as $value)
                    {
                        $res[$value] = $row[$value];
                    }
                    $num_rows++;
                    $result[] = $res;
                }
                $this->num_rows = $num_rows;
            }
            if ($num_rows <= 1)
            {
                $this->getFirstRow = $result[0];
                $this->getLastRow = $result[0];
            }
            else
            {
                $this->getFirstRow = $result[0];
                $last_row_id = $num_rows - 1;
                $this->getLastRow = $result[$last_row_id];
            }
            return $result;
        }
        else
        {
            return false;
        }
    }

    function SQLite3_Insert($query)
    {
        if (is_string($query))
        {
            try
            {
                $this->DB->exec($query);
            } catch (Exception $e)
            {
                return false;
            }
        }
    }

    function SQLite3_Update($query)
    {
        $this->SQLite3_Insert($query);
    }

    function SQLite3_Delete($query)
    {
        $this->SQLite3_Insert($query);
    }

    function SQLite3_CreateTable($TableName, $TableStructure)
    {
        $query = "SELECT count(*) FROM sqlite_master WHERE type='table' AND name='$TableName'";
        $table_avail = $this->DB->querySingle($query);
        if ($table_avail == 0)
        {
            $this->DB->exec($TableStructure);
        }
    }

    function SQLite3_DropTable($TableName)
    {
        $query = "SELECT count(*) FROM sqlite_master WHERE type='table' AND name='$TableName'";
        $table_avail = $this->DB->querySingle($query);
        if ($table_avail == 1)
        {
            $this->DB->exec("DROP TABLE $TableName");
        }
    }

    function __destruct()
    {
        if ($this->DB)
        {
            $this->DB->close();
        }
    }

}
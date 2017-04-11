<?php

/*
 *
 * ===============================================
 * Name: class.db.oracle.php
 * ===============================================
 * Beschreibung:
 * Klasse für die vereinfachte Oracle Verwendung
 *
 * ===============================================
 *
 * $Author$
 * $LastChangedDate$
 * $LastChangedBy$
 * $Id$
 * $Revision$
 *
 */

class eylib_oracle extends eylib
{

	private $DB_Handle;
	private $DB_Host;
	private $DB_ServiceName;
	private $DB_UserName;
	private $DB_Password;
	private $DB_Charset;

	public $num_rows;


	function __construct($DB_ORACLE)
	{
		if (is_array($DB_ORACLE))
		{

			$this->DB_Host = $DB_ORACLE['DB_SERVER_IP'];
			$this->DB_ServiceName = $DB_ORACLE['DB_SERVICE_NAME'];
			$this->DB_UserName = $DB_ORACLE['DB_USER_NAME'];
			$this->DB_Password = $DB_ORACLE['DB_USER_PASSWORD'];
			$this->DB_Charset = $DB_ORACLE['CHARSET'];
			$this->oracle_db_connect();
		}
		else
		{
			$this->error_handler("Unbekannter DB Identifier: $DB_IDENTIFIER_NAME", TRUE);
			die();
		}

		parent::__construct();

	}


	private function oracle_db_connect()
	{
		$this->DB_Handle = oci_connect("$this->DB_UserName", "$this->DB_Password", "//$this->DB_Host/$this->DB_ServiceName", "$this->DB_Charset");
		if (!$this->DB_Handle)
		{
			$this->error_handler("Kann mich nicht verbinden: " . OCIError(), TRUE);
			die();
		}

	}


	private function oracle_db_close()
	{
		ocilogoff($this->DB_Handle);

	}


	public function oracle_db_select($query)
	{
		$oracle_statement = @ociparse($this->DB_Handle, $query);
		@oci_execute($oracle_statement);



		while ($row = @oci_fetch_assoc($oracle_statement))
		{
			foreach($row as $key => $value)
			{
				$key = strtolower($key);
				$lower_row[$key] = $value;
			}

			$retArray[] = $lower_row;
		}

		$this->num_rows = oci_num_rows($oracle_statement);
		oci_free_statement($oracle_statement);
		$this->oracle_db_close();

		return $retArray;

	}

}

?>
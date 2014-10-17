<?php
	class MySQLDataBase {
		public $host;
		public $user;
		public $pswd;
		public $schm;
		public $port = 3306;

		/** @var mysqli */
		public $conn = false;

		public $sql = '';

		/** @var mysqli_result */
		public $result = false;
		public $err = array();

		function __construct($host='',$user='',$pswd='',$schm='') {
			$this->host = ($host=='' ? MIB_DB_HOST : $host);
			$this->user = ($user=='' ? MIB_DB_USER : $user);
			$this->pswd = ($pswd=='' ? MIB_DB_PWD : $pswd);
			$this->schm = ($schm=='' ? MIB_DB_SCHEMA : $schm);
		}

		// Execute a query with no results
		function execute($sql = '',$buffer=false) {
			if($sql != '') $this->sql = $sql;

			$this->connect();

			$this->result = false;
			if($buffer) $this->result = $this->conn->query($this->sql);
			else $this->result = $this->conn->query($this->sql);

			if(!$this->result) {
				$this->err[] = 'Error occurred executing query ('.$this->sql.')';
				debug($this);
			}
		}

		// Get a single value from database
		function getValue($sql = '') {
			if($row = $this->getRow($sql,false)) return $row[0];
			else return false;
		}

		/**
		 * @param $sql
		 * @param bool $as_associative_array
		 * @return array|bool|mixed
		 */
		function getRow($sql = '', $assoc=true) {
			$this->connect();
			$row=array();

			if ($this->result = $this->conn->query($sql)) {
				if ($assoc) {
					$row = $this->result->fetch_assoc();
				} else {
					$row = $this->result->fetch_row();
				}

				/* free result set */
				$this->result->free_result();
				$this->result = null;
			}
			return $row;
		}

		// Get a single column from database as an array
		// or Get two columns from database as an associative array
		function getCol($sql = '',$assoc=false) {
			$this->execute($sql,true);

			$data = array();
			while($row = $this->result->fetch_row()) {
				if($assoc) $data[$row[0]] = $row[1];
				else $data[] = $row[0];
			}			
			$this->result->free_result();
        		$this->result = null;
			return $data;
		}

		// Get two dimensional array from database
		function getData($sql = '',$assoc_col=true,$assoc_row=false) {
			$this->execute($sql,true);

			$data = array();
			if($assoc_col) {
				while($row = $this->result->fetch_assoc()) {
					if($assoc_row) {
						$key = array_shift($row);
						$data[$key] = $row;
					} else {
						$data[] = $row;
					} 
				}
			} else {
				while($row = $this->result->fetch_row()) {
					if($assoc_row) {
						$key = array_shift($row);
						$data[$key] = $row;
					} else {
						$data[] = $row;
					} 
				}
			}
			$this->result->free_result();
        		$this->result = null;
			return $data;
		}

		/**
		 * @return int
		 */
		function getAffectedRows() {
			return $this->conn->affected_rows;
		}

		/**
		 * @return mixed
		 *
		 * getID: Gets the auto increment value used in the previous insert statement.
		 */
		function getID() {
			return $this->conn->insert_id;
		}


		// Ensure database is connected
		function connect($reconnect = false) {
			if($reconnect) {
				if(!mysqli_ping($this->conn)) {
					$this->disconnect();
				}
			}

			if(!$this->conn) {
				//$this->conn = mysql_connect);            
				$this->conn = new mysqli($this->host,$this->user,$this->pswd, $this->schm, $this->port);

				if(!$this->conn) $err[] = 'Could not connect to database on '.$this->host.' as '.$this->user;
			}
		}

		// Force a disconnect from the database
		function disconnect() {
			if($this->conn) {
				mysql_close($this->conn);
				$this->conn = false;
			}
		}
	}
?>

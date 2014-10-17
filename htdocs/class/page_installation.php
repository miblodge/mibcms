<?php
	includeClass('class_page.php');

	class InstallationPage extends Page {
		public $admin = false;
		public $tables = array();
		public $issues = array();

		function __construct($db,$auth) {
			$this->db = $db;
			$this->auth = $auth;
			$this->messages = array();

			if(defined('MIB_SITE_TITLE')) $this->site_title = MIB_SITE_TITLE;

			// Is this user a superuser or admin?
			$this->admin = true; // TO DO: Replace with actual check, this currently means anyone can run this page...

			$this->checkDBSchema();

			if(isset($_GET['table'])) {
				$action = $_GET['action'];

				$this->addMessage($this->tables[$_GET['table']]->performAction($action));
				$this->checkDBSchema();
			}

			// Get list of issues
			$issues = array();
			foreach($this->tables as $name => $table) {
				$table_issues = $table->check();
				if(!empty($table_issues)) {
					$issues = array_merge($issues,$table_issues);
				}
			}
			$this->issues = $issues;

			if(empty($this->issues)) {
				// All done, so redirect to main page.
				header('Location: index.php');
			} else {
				if(!$this->auth->config_admin) {
					//redirect to error.php as not admin
					header('Location: error.php');					
				}
			}
		}

		function checkDBSchema($tablename = '') {
			// Perform Database Checks
			$db_schema = simplexml_load_file(getFileToUse('database.xml'));
			//debug($db_schema);

			foreach($db_schema->children() as $child) {
				if($child->getName() == 'table') {
					$name = '';
					$attributes = $child->attributes();
					$name = (string) $attributes['name'];

					$this->tables[$name] = new DBTable($this->db,$name);
					foreach($child->children() as $gchild) {
						// Stuff common to both fields and indexes
						$gattributes = $gchild->attributes();
						$gname = (string) $gattributes['name'];

						switch($gchild->getName()) {
							case 'field':
								$type = (string) $gattributes['type'];
								$default = (string) $gattributes['default'];
								$notnull = ((string) $gattributes['notnull'] == 'true');
								$auto_increment  = ((string) $gattributes['auto_increment'] == 'true');
								$this->tables[$name]->addField($gname,$type,$default,$notnull,$auto_increment);
								break;
							case 'index':
								$this->tables[$name]->addIndex($gname);
								break;
						}
					}
				}
			}
		}

		function renderIssues() {
			foreach($this->issues as $issue) {
				include($this->getTemplate('issues_table_row.php'));
			}
		}

		function renderContent() {
			parent::renderContent();
			if(!empty($this->issues)) include($this->getTemplate('issues_table.php'));
		}

		function getContent() {
			$html_output = '';
			$html_output .= '<p>Welcome '.$this->auth->name.' to '.$this->site_title.'.</p>';

			if($this->admin) {
				// display list of issues to resolve, with links to resolve them
				$html_output .= '<p>Issues to resolve...</p>';
			} else {
				// display list of issues to resolve, with links to resolve them
				$html_output .=  '<p>This site is currently under maintanence.</p>';
			}

			return $html_output;
		}
	}

	class DBIndex {
		public $name = '';

		function __construct($name) {
			$this->name = $name;
		}
	}

	class DBField {
		public $name = '';
		public $type = '';
		public $default = '';
		public $notnull = false;
		public $auto_increment = false;

		function __construct($name,$type,$default,$notnull,$auto_increment) {
			$this->name = $name;
			$this->type = $type;
			$this->default = $default;
			$this->notnull = $notnull;
			$this->auto_increment = $auto_increment;
		}

		function __get($name) {
			switch($name) {
				case 'definition':
					//include type, not null and default value but not autoincrement
					$definition = $this->type;
					if($this->notnull) $definition .= ' not null';
					if($this->default != '') {
						$definition .= ' default ';
						if($this->default == 'null' or $this->default == 'current_timestamp') $definition .= $this->default;
						else $definition .= '"'.$this->default.'"';
					}
					return $definition;
					break;
				case 'primary_key':
					if($this->auto_increment) return true;
					return false;
					break;
				case 'create_sql':
					$sql = $this->name.' '.$this->definition;
					if($this->auto_increment) $sql .= ' auto_increment';
					return $sql;
					break;
			}
		}
	}

	class DBTable {
		public $db = null;
		public $name = '';
		public $fields = array();
		public $indexes = array();

		public $issues = array();

		function __construct($db,$name) {
			$this->db = $db;
			$this->name = $name;
		}

		function __get($name) {
			switch($name) {
				case 'primary_keys':
					$primary_keys = array();
					foreach($this->fields as $field) {
						if($field->primary_key) $primary_keys[] = $field->name;
					}
					return $primary_keys;
					break;
				case 'pk_sql_part':
					$pk_array = $this->primary_keys;
					if(empty($pk_array)) return '';
					else return ', primary key ('.implode(', ',$this->primary_keys).')';
					break;
				case 'field_sql_parts':
					$parts = array();
					foreach($this->fields as $field) {
						$parts[] = $field->create_sql;
					}
					return $parts;
					break;
			}
		}

		function addField($name,$type,$default,$notnull,$auto_increment) {
			$this->fields[$name] = new DBField($name,$type,$default,$notnull,$auto_increment);
		}

		function addIndex($name) {
			$this->indexes[$name] = new DBIndex($name);
		}

		function addIssue($issue,$suggest,$action,$db_table_name) {
			$tmp = array('issue'=>$issue,'suggest'=>$suggest,'action'=>$action,'table'=>$db_table_name);
			$this->issues[] = $tmp;
		} 

		function check() {
			// First check if table exists in database
			if($this->exists()) {
				// Table exists, make sure it matches schema
			} else {
				// Table doesn't exist, don't bother checking for further problems.
				$this->addIssue("Table doesn't exist","Create Table",'create',$this->name);
			}

			return $this->issues;
		}

		function exists() {
			$expected_table_name = MIB_DB_PREFIX.$this->name;
			$sql = 'SHOW tables LIKE "'.$expected_table_name.'"';
			$tables = $this->db->getCol($sql);
			return (count($tables) == 1);
		}

		function create() {
			// First, check table needs creating...
			if(!$this->exists()) {
				// Table needs to be created
				$table_name = MIB_DB_PREFIX.$this->name;

				$sql = 'create table `'.$table_name.'` ('.implode(', ',$this->field_sql_parts).' '.$this->pk_sql_part.')';
				$this->db->execute($sql);

				//debug($sql);
				if($this->exists()) return array('message'=>'Successfully created table '.$this->name.'.');
				else return array('warning'=>'Failed to create table '.$this->name.'.');
			} 
			return array('warning'=>'Table appears to already exist.');
		}

		function performAction($action) {
			switch($action) {
				case 'create':
					return $this->create();
					break;
				default:
					die('Attempt to perform unhandled action ('.$action.') on table ('.$this->name.')');
					break;
			}
		}
	}
?>

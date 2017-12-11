<?php

class Database
  extends _database_base {
    use middleware;

  //connection reference
  private $_connection = null;

  //individual connection parameters.
  private $_hostname;
  private $_username;
  private $_password;
  private $_database;

  #path to the extended model directory
  private $_modelpath = null;

  /*
   * schema cache will store model schemas that have been previous requested.
   * this will prevent database desrbive queries from being execute every time
   * the same schema is crated.
   */
  private $_schemas = array();

  //DEBUGGING SETTINGS
  //debug flag
  private $_query_reporting = false;
  //number of queries executed
  private $_executed_query_count = 0;
  //list of executed queries
  private $_executed_query_list = array();



  public function __construct($args = null) {

    #valid flag.
    $valid = true;

    if(!is_null($args)) {

      foreach($args as $key => $param) {

        $method = 'set' . $key;

        #if the method exists, call it and pass the value.
        if(method_exists($this, $method)) {
          $this->$method($param);
        }
      }
    } else {

      $valid = false;
    }

    /*
      *if the parameters are valid, create a new connection object
      */
    if($valid) {

      $this->_connection = $this->get_connection();
    }
  }

  /*
   *setters
   */

  public function setHostname($val) {

    $this->_hostname = $val;
  }
  public function setUsername($val) {

    $this->_username = $val;
  }
  public function setPassword($val) {
    $this->_password = $val;
  }
  public function setDatabase($val) {
    $this->_database = $val;
  }
  public function setModels($val) {
    $this->_modelpath = $val;
  }

  // Set the timezone for each Session
  public function setTimezone($val) {
    $sql = "SET `time_zone` = '" . $val . "'";
    return $this->execute($sql);
  }

  /*
   *@method nothing __destruct() - destructor method for the database class.
   */
  public function __destruct() {
    //clean up.
    //if query reporting is set to true. display the number of queries executed.
    if($this->_query_reporting) {
      debug::show_var($this->_executed_query_count, "database.__descruct()", "execute_query_count");
      debug::show_var($this->_executed_query_list, "database.__descruct()", "_executed_query_list");
    }
  }

  /*
   *@method int execute($sql) - runs the submitted query
   *@param string $sql - the sql statement to execute.
   *@return int = number of rows affected if on success.
   */
  public function execute($sql) {
    return $this->execute_query($sql);
  }

  /*
   * @method query() - executes a query.
   * @param String query - Query string to execute.
   * @return Mixed - returns an array on success or false on railure.
   */
  public function query($sql) {
    return $this->execute_query($sql);
  }

  public function newQuery() {
    return new Query($this);
  }

  /*
   *build_db_row($tableName) - attempts to load the generic_database_row file and create a row object for that table.
   *
   *@param string $tableName - name of the table of which to build the object of.
   *
   *@return generic_database_row object - returns the object of the type generic_database_row.
   */
  public function getRow($name) {

    //get the row data
    if($schema = $this->describe($name)) {

      //create a new Row object.
      return new Row(array('schema'=>$schema,
                           'tablename'=>$name,
                           'database'=>$this));

    } else {

      $err = "database->build_db_row()->describe(): Did NOT return valid field data.";
      $this->error([$err]);

      return null;
    }
  }

  /*
   * @method model - Alias for 'getModel'
   */
  public function model($name) {
    return $this->getModel($name);
  }

  public function getModel($name) {

    //get the row data
    if($schema = $this->describe("`$name`")) {

      //create a new Model object.
      //Todo: Can probably assign the _Middleware as a parameter to preven the looping durring middlware assignment.
      return new Model(array('schema'=>$schema,
                             'tablename'=>$name,
                             'database'=>$this,
                             '_middleware' => $this->_middleware));
    } else {

      $err = "database->build_db_row()->describe(): Did NOT return valid field data.";
      $this->error([$err]);

      return null;
    }
  }

  /*
   *@method getSchema() - returns a table schema as an array.
   *@param String $tableName - Name of the table to describe.
   *@return mixed - returns an array on success. or null of false.
   */
  public function getSchema($tableName) {

    if(empty($tableName) || !is_string($tableName)) {

      $err = get_called_class() . ": Invalid table name supplied";
      $this->error([$err]);
      return null;
    }

    return $this->describe($tableName);
  }


  public function escape_string($string) {
    $conn = $this->get_connection();
    $string = $conn->real_escape_string($this->mysql_prep($string));
    return $string;
  }
  public function escape($string){
    return $this->escape_string($string);
  }

  public function clean($val) {
    return $this->escape_string($val);
  }
  /*****************************************PRIVATE FUNCTIONS***************************************/

  private function get_connection(){

    //if connection already open return the reference to the private connection variable
    if($this->_connection != false && get_class($this->_connection) == 'mysqli') {
      return $this->_connection;
    } else {
      //if connection does not exists open and store it in the local variable and return
      //reference to that variable.
      $connection = new \mysqli($this->_hostname, $this->_username, $this->_password, $this->_database);

      if(!$connection){
        $this->_errors[] = $connection->error;
        return false;
      } else {
        $this->_connection = $connection;

        //try to set the character set and return false on fail.
        if (!$this->_connection->set_charset('utf8')) { return false; }

        return $this->_connection;
      }
    }
  }

  private function mysql_prep($value) {
    $magic_quotes_active = get_magic_quotes_gpc();
    //check if the function maed mysql_real_escape_string exists which means
    //that the php version is above 4.3 and therefore has the function built in
    //automatically.
    $new_enough_php = function_exists("mysql_real_escape_string"); //PHP > v4.3.0

    if($new_enough_php) { //version 4.3.0 or higher undo any magic quotes effects so
      //mysql_real_escape_string can do the work
      if($magic_quotes_active) { $value = stripslashes($value); }
    } else { //before php v4.3.0
      //if magic quotes aren't already on then add slashes manually
      if(!$magic_quotes_active) {$value = addslashes($value); }
      //if magic quotes are active, then the slashes already exist
    }
    return $value;
  }

  private function execute_query($query, $raw = false){
    //if the debug flag: $_query_reporting is set to true, store the number of queries run, and each query.
    if($this->_query_reporting) {
      $this->_executed_query_count += 1;
      $this->_executed_query_list[] = $query;
    }

    if($db_connection = $this->get_connection()) {
      $result = $db_connection->query($query);

      if(is_object($result) && (get_class($result) == 'mysqli_result')) {

        //select, describe, show or explain queries return a mysqli_result object
        //if the raw mysqli_result object is set to true return that.
        if($raw === true) { return $result; }

        //if only the array is required, fetch the result and return them
        $result_set = array();

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          $result_set[] = $row;
        }

        return $result_set;

      } elseif($result) {
        /*
         * Retrieve the insert_id and the affected_rows.
         * If the affected rows is > 1, that means the operation was either an
         * UPDATE, DELETE, or INSERT. If there is an insert_id AND an affected_rows
         * value, return the larger value. This is and edge case when inserting more than
         * a single value, in which case, having the affected_rows is more usefull.
         */
         $insert_id = isset($db_connection->insert_id) &&
           is_numeric($db_connection->insert_id) ? $db_connection->insert_id : 0;

         $affected_rows = isset($db_connection->affected_rows) &&
           is_numeric($db_connection->affected_rows) ? $db_connection->affected_rows : 0;

           return $insert_id > $affected_rows ? $insert_id : $affected_rows;
      } else {

        $this->error([
                       __CLASS__,
                       __METHOD__,
                       $db_connection->error
                     ]);
        return null;
      }
    }
  }

  /*
   *describe($tableName) - runs a Describe query on the given table name, and returns the results.
   *@param string $tableName - name of the table to describe
   *@return array $field_data - the assositive array of the field properties.
   */
  private function describe($tableName) {
    if(empty($tableName) || !is_string($tableName)){
      $err = "database.describe($tableName): Invalid Table name supplied.";

      $this->error([$err]);

      return false;
    }

    #check if the schema exists in the cache
    if(isset($this->_schemas[$tableName]) &&
      !empty($this->_schemas[$tableName]) &&
      is_array($this->_schemas[$tableName])) {

      #return the schema.
      return $this->_schemas[$tableName];
    }

    //build the query
    $sql = "DESCRIBE " . strtolower($this->escape_string($tableName));

    $result = $this->execute_query($sql);
    if(!$result) {
      $err = "database.describe($tableName): Table does not exist";
      $this->error(['error'=> $err, 'method'=>__METHOD__]);

      return false;
    }

    return $result;
  }
}

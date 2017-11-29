<?php

class Row
  extends _database_base
  implements \Iterator {

  use middleware;

  //control flags array.
  private $_flags = null;

  // multiple insertion flag. Sigifies that multiple values are to inserted.
  protected $_multiple = false;

  // Items array of items to be inserted, if the _multiple flag is set to true.
  private $_items;

  /* Metadadta cotainer that will store extra information attached to the Model
   * that is not stored with the model.
   */
  protected $_metadata = [];

  // Constructed SQL from the specified operation.
  protected $_sql;

  //raw field data
  protected $_schema;

  #database reference.
  protected $_database;

  //table name
  protected $_tableName;

  //field - array to store the generic_field_objects
  private $_fields = array();

  //store the name of the primary field
  private $_primaryField = null;

  //is empty flag.
  private $_empty = true;

  /*
   *__constructor - builds the generic_database_row.
   *@param array $row_data - assocciative array that contains the information about the fields in the table.
   *@param string $tableName - name of the table.
   */
  public function __construct($args) {

    #call the parent constructor to set the properties.
    parent::__construct($args);

    //process the data
    $this->process();
  }

  protected function setSchema($val) {

    $this->_schema = $val;

  }

  protected function setDatabase($val) {

    $this->_database = $val;

  }

  /*
  *@method getSchema() - get the table Schema as an associative array
  *@return array = returns an associative array of table data.
  */
  public function getSchema() {
    return $this->_schema;
  }

  public function getDatabase() {
    return $this->_database;
  }

  /*
   * @method isField() - Check if the current model has a field with the supplied name
   * @param String - Field name.
   * @return Boolean - Returns true if field exists, false otherwise.
   */
  public function isField($val) {

    #validate for a valid string.
    if(!is_string($val) || empty($val)) {
      $this->error(array('error'=>"Invalid Field `name` supplied",
                         'method'=>__METHOD__));
      return false;
    }

    #check if the field with the supplied name is set in the fields array.
    return isset($this->_fields[$val]);
  }

  protected  function setTablename($val) {
    $this->_tableName = $val;
  }

  /*
   *@method getTableName() - Get the name of the database table associated with the current instance of the model.
   *@return String - Name of the database table as a String.
   */
  public function getTablename() {

    return $this->_tableName;

  }

  /*
   *process() - build the generic_database_field objects for each raw_data row and store them.
   *  in an associative array using the field name as key.
   */
  private function process() {
    //check the valid flag if everything passed validation.
    if(!$this->_valid) {
      //build the error and set it
      $err = "generic_database_row.process(): Invalid flag is set, Processing stopped.";
      $this->setError($err);
      return false;
    }

    //valid flag is true proceed.
    //loop through the _schema and build a generic_databause_field object.
    foreach($this->_schema as $field_data) {

      //make a new generic_database_field object
      $field = new Field($field_data);
      //store the field inside the _field array using the field_name as the key
      $this->_fields[$field->getName()] = $field;
      //if the field is a primary key. set the field name in the private property.
      if($field->isPrimaryKey()) { $this->_primaryField = $field->getName();  }

    }
  }

  /*
   *set_field_values($values) - loop thru the array, and if the key inside the values exists inside the fields, set that value.
   *@param array $valus = assocciative array with the key as field names.
   */
  public function setValues($values = array()) {

    if(empty($values) || !is_array($values)) {
      //set the error
      $err = "supplied value is either empty or is an invalid type.";
      #if invalid set an error message
      $this->error(array('error' => $err,
                         'method' => __METHOD__,
                         'arguments'=> func_get_args()));
      return false;
    }
    //loop and match they keys, set the matching keys values.
    foreach($values as $key => $value) {
      //if the field with value of key is set, run its setValue method to set the value.
      if(isset($this->_fields[$key]) && !empty($this->_fields[$key])) {
        $this->_fields[$key]->setValue($value);
      } else {
        // If the field object does not exists for the key, add the data as metadata.
        $this->meta($key, $value);
      }
    }
    #Set the empty flag.
    $this->_empty = false;
    return $this;
  }

  /*
   *@method meta() - Sets or gets meta data at the specified key.
   *@key String - Key at whicht to store the data.
   *@value Mixed - Metadata to be store at #key.
   *@return Mixed - return value or null if retrieving data, or true if settings valid data.
   */
  public function meta($key, $value = null) {
    if(!is_string($key)) { return null; }

    if(!is_null($value)) {
      $this->_metadata[$key] = $value;
      return true;
    }

    if(is_null($value) && isset($this->_metadata[$key])){
      return $this->_metadata[$key];
    }

    return null;
  }

  /*
   *@method getPrimaryField() - returns the name of the primary key field
   *@return string - primary_key field name
   */
  public function getPrimaryKeyName() {

    //if the primaryField name is set, return it.
    if(!is_null($this->_primaryField) && !empty($this->_primaryField)) { return $this->_primaryField; }

    //if the primaryField property is not set, loop throught the fields array to find the primary field.
    if(empty($this->_fields) && !is_array($this->_fields)) {
      $err = 'generic_database_row.pri_field_name(): Could not retrieve Primary_key name. Field properties are empty or invalid';
      $this->setError($err);
      return null;
    }

    //loop thru the fields, and find the primary key field.
    foreach($this->_fields as $field) {
      if($field->isPrimaryKey()) { return $field->getName(); }
    }

    //if not found return false.
    return null;
  }

  /*
   *@method getPrimaryField() - returns the name of the primary key field
   *@return string - primary_key value
   */
  public function getPrimaryKey() {

    if(empty($this->_fields) && !is_array($this->_fields)) {
      $err = 'generic_database_row.pri_field_name(): Could not retrieve Primary_key name. Field properties are empty or invalid';
      $this->setError($err);
      return null;
    }

    //if the primaryField name is set, return its value
    if(!is_null($this->_primaryField) && !empty($this->_primaryField)) {
      return $this->_fields[$this->_primaryField]->getValue(); }


    //if the private property is not set.
    //Loop thru the fields, and find the primary key field.
    foreach($this->_fields as $field) {
      if($field->isPrimaryKey()) { return $field->getValue(); }
    }

    //if not found return false.
    return null;
  }

  /*
   *@method getField() - Returns a field Object with the supplied name or null.
   *@param String $name - Name of the fields to search for.
   *@return mixed - Returns a Field Object on success or NULL on failure.
   */
  public function getField($key) {
    return isset($this->_fields[$key]) && !empty($this->_fields[$key]) ?
      $this->_fields[$key] : null;
  }

  /*
   *validate() - loop thru each field object and run its validate method. if invalid,
   *  retrieve the error message and set the valid flag to false.
   *@return bool - returns false if an error was found.
   */
  public function validate() {

    #reset the valid flag
    $this->_valid = true;

    // Local error array.
    $errors = [];

    #loop and run the validate method for each field.
    foreach($this->_fields as $field) {

      #if not valid get the error messages, and store them inside the errors array.
      if(!$field->validate()) {

        // Store the error localy.
        $errors[] = $field->getErrors();

        #set the valid flag to false.
        $this->_valid = false;

      }
    }
    if(!$this->_valid) {
      //$this->error(['error'=> $field->getErrors(), 'method'=>__METHOD__]);
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     "Validation Failed",
                     $errors
                   ]);
    }


    return $this->_valid;
  }

  /*
   *@get() - gets a value of the supplied field_name if one is set.
   *@param string $name - The name of the field whose value is required.
   *@return string - the return value of the field.
   */
  public function get($name) {

    if(is_string($name) && isset($this->_fields[$name])) {
      return $this->_fields[$name]->getValue();
    }
    return null;
  }

  /*
   *@method bool set($name, $value) - sets the value of the $name field, to the $value;
   *@param string @name - the name of the target field
   *@param string @value - the value to set.
   *@param boolean @is_func - flag the is set to true if the value of the field is a mysql function.
   *@return bool - return true on success or false on failure
   */
  public function set($name, $value = null, $is_funk = null) {

    if(is_array($name)) { return $this->setValues($name); }

    if(!is_string($name) || !is_scalar($value)){
      //set the error
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Field name or value is invalid.'
                   ]);

      return $this;
    }
    //check if the field with the $name parameter exists
    if(!$this->isField($name)) {

      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Field name \'' . $name . '\' does not exist.'
                   ]);

      return $this;
    }

    //set the the field value.
    $this->_fields[$name]->setValue($value, $is_funk);
    //Set the empty flag to false;
    $this->_empty = false;

    return $this;
  }

  /*
   * @method mulitple() - Initializes the insertion of mutiple rows.
   * @param Array $items - Array of arrays that contains data to be inserted.
   */
   public function multiple(Array $items = []) {
     $this->_multiple = true;
     $this->_items = $items;

     return $this;
   }


  /*
   *@method toArray() - get an Assoccative array of key values pairs.
   *@return Mixed - returns an assocciative array on success, or null on failure.
   */
  public function toArray() {
    if($this->_empty || !is_array($this->_fields) || empty($this->_fields)) {
      return [];
    }
    //output array.
    $output = array();
    //loop thought and extract each field name and value
    foreach($this->_fields as $field) {
      $output[$field->getName()] = $field->getValue();
    }
    return $output;
  }

  /*
   *@method string insert() - builds and insert quiery from the field object names and values, and returns it.
   *@param Array $args - Arguments array with various control flags.
   *@return string - returns the build insert sql string.
   */
  public function insert(Array $args = []) {
    // If not inserting multipe record, and the field does not validate, return $this and store errors.
    if($this->_multiple !== true && !$this->validate()) {
      return $this;
    }

    /*
     *@method values - Builds an sql string of the values to be inserted into the table.
     *@return String - Return a proper values string to be included in the VALUES clause of the query.
     */
    $values = function() {
      $sql = '(';
      $fCount = count($this->_fields);
      $i = 0;

      foreach($this->_fields as $field) {
        ++$i;
        //if primary field, skip to the next one
        if($field->getKey() == 'PRI') { continue; }

        // Get the properly fomatted 'insert' value. This is value represent the value that would be inserted
        // Into the database. Either the use set value, or the default value that is properly escaped.
        $sql .= $field->getInsertValue();

        // Add the '(' or ',' depending on if this is the last field.
        $sql .= $i === $fCount? ')' : ',';

      }
      return $sql;
    };

    // Build the Field names.
    $sql = 'INSERT ';

    // Check if Ignore flag is set.
    $sql .= isset($args['ignore']) && $args['ignore'] === true ? 'IGNORE ' : '';

    $sql .= 'INTO ' . "`$this->_tableName`";

    // Escape Function
    $escape = function($n) {
      return "`$n`";
    };
    
    // Set the field names.
    $sql .= '(' . implode(',', array_map($escape, $this->getFieldNames())) . ')';

    $sql .= ' VALUES';

    // Build the VALUES clause based on if inserting multiple records.
    if($this->_multiple === true) {
      $count = count($this->_items);
      for($i = 0; $i < $count; ++$i) {
        $value = $this->_items[$i];
        $this->setValues($value);
        if(!$this->validate()) {
          //Todo: Record the failing data items.
          return $this;
        }
        $sql .= $values();
        $sql .= ($i+1) === $count ? '' : ',';
      }
    } else {
      $sql .= $values();
    }

    $this->_sql = $sql;

    return $this;
  }

  /*
   *@method getFieldNames() - Get field names.
   *@return Array - Returns array of field names.
   */
  public function getFieldNames(Array $args = []) {
    $fields = [];
    foreach($this->_fields as $field => $value) {
      if($value->isPrimaryKey()) { continue;}
      $fields[] = $value->getName();
    }
    return $fields;
    //return array_keys($this->_fields);
  }

  /*
   *@method getValues() - Get field values that would be inserted into the database.
   * These are not necessarily the values inside the 'value' property. If the value is empty, the
   * default value as specified by the schema will be used.
   *@return Array - Returns an array of field value.
   */
  public function getInsertValues() {
    $values = [];
    foreach($this->_fields as $field => $value) {
      if($value->isPrimaryKey()) { continue;}
      $values[] = $value->getInsertValues();
    }
    return $values;
  }

  /*
   *
   */

  /*
   *@method string get_update() - builds and update quiery from the field object names and values, and returns it.
   *@param string $condition - the update condition for the where clause.
   *@return string - returns the build insert sql string.
   */
  public function update($flags = array()) {

    //validate the results.
    if(!$this->validate()) {
      return $this;
    }

    $sql = '';

    $sql .= "UPDATE " . $this->_tableName . " SET ";
    foreach($this->_fields as $field) {

      //if the fieldtype is timestamp, skip it.
      if($field->getMySQLType() == 'timestamp' || $field->isPrimaryKey()) {
        continue;
      }

      //get the field value
      $value = $field->getValue();
      //if the value is empty, skip the field
      if(empty($value)) { continue; }
      //get the name;
      $name = $field->getName();

      //pass the value to addslahes to escape the ', ", /, and null.
      $sql .= $name . '="' . $this->_database->escape_string(addslashes($value)) . '",';
    }
    //remove the trailing comma
    $sql = rtrim($sql, ',');

    //set the where cause update condition
    $sql .= " WHERE ";
    $sql .= $this->getPrimaryKeyName() . "=" . $this->getPrimaryKey();

    $this->_sql = $sql;

    return $this;
  }

  /*
   *@method delete($condition, $flags)
   *@param array $flags - array of control flags.
   */
  public function delete() {
    //build the delete query
    $sql = '';
    $sql .= "DELETE FROM " . $this->_tableName . " WHERE ";
    $sql .= $this->getPrimaryKeyName() . "=" . $this->getPrimaryKey();

    $this->_sql = $sql;
    return $this;
  }

  /*
   *@method string exec() - builds the sql query from the field object names and values, and returns it.
   *@param Boolean $return - Flag to determines if the results of the query are to be returned directly or stored in the model.
   *@return Mixed - returns a result of execute the sql query.
   */
  public function exec($return = false) {

    // Trim off the whitespace in the quries.
    $this->_sql = trim($this->_sql);

    if(!$this->_sql) {
      // Create an error message.
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'SQL Query value is invalid.'
                   ]);
      return null;
    }

    //Process and execute the sql statement.
    $action = strtolower(substr($this->_sql, 0, strpos($this->_sql, ' ')));

    // Run the middleware.
    $this->runMiddlware('pre', $action);

    $result = $this->_database->execute($this->_sql);
    //Reset the sql to prevent subsequent calls to exec().
    $this->_sql = null;

    if($this->_database->hasErrors()) {
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     $this->_database->getErrors()
                   ]);
      return null;
    }

    //Custom operation post Processing
    switch($action) {
      case 'insert':
        // If this is a single insert operation, set the return value as the
        // primary key of the current model.
        if(!$this->_multiple){
          //if id is returned. set it in the entity_id field.
            $this->set($this->getPrimaryKeyName(), $result);
        }
      break;
      case 'select':
        //Select statements return an array with the result being at index 0;
        //Extract the result array at index 0;
        if(count($result) === 1 && isset($result[0]) && is_array($result[0])){
          $this->setValues($result[0]);
        }
      break;
      default:
      break;
    }

    // Run the middleware.
    $this->runMiddlware('post', $action);

    // If the return flag is set to true, return the result. Otherwise return $this.
    return $return === true ? $result : $this;
  }

  /*
   *@method reset() - clears the field values, and errors.
   */
  public function reset() {
    if(is_array($this->_fields) && !empty($this->_fields)) {
      foreach($this->_fields as $field) {
        //clear the value
        $field->clearValue();
        //clear the errors
        $field->clearErrors();
      }
    }
    $this->_empty = true;
    return $this;
  }

  /*
   * @method sql() - sets or returns the sql string.
   * @param String - SQL String to set.
   * @return Mixed - Returns the sql string on sucess, or null on failure.
   */
  public function sql($sql = null) {
    if(is_string($sql)) {
      $this->_sql = $sql;
      return $this;
    }
    return $this->_sql;
  }

  /*
   * @method isEmpty() - Check if the model contains data.
   */
  public function isEmpty() {
    return $this->_empty;
  }

  /*******************************ITERATOR ABSTRACT METHODS********************/

  public function current() {
    return current($this->_fields);

  }

  public function key() {
    return key($this->_fields);
  }

  public function next() {
    return next($this->_fields);
  }

  public function rewind() {
    return reset($this->_fields);

  }

  public function valid() {
    return key($this->_fields) !== null;
  }

}

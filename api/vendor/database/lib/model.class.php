<?php
/*
 *Model base class.
 */

class Model extends row {
  /****************************PUBLIC METHODS*******************************/
  public function __construct($args = array()) {
    // Call the parent constructor to set the properties.
    parent::__construct($args);
  }

  public function __set($name, $value) {
  }

  public function __get($name) {
  }

  public function __destruct() {
  }

  /*
   *@method toArray() - Calls parents toArray() method, and attaches any metadata
    added to the model.
    @return Array - Returns array of field data and meda data.
   */
  public function toArray() {
    $result = parent::toArray();
    // Check metadata exists, set it in the results array.
    if(!empty($this->_metadata)){
      foreach($this->_metadata as $key => $value) {
        // Check if the value with the same key already exists.
        if(!isset($result[$key])){
          $result[$key] = $value;
        } else {
          // Rename the key and set the data.
          $result['_' . $key] = $value;
        }
      }
    }
    return $result;
  }

  /*
   *@method load() - Retrieve the record using its Primary Key value.
   *@param Int $id - Integer Primary Key.
   *@return boolean - returns true on sucess, false on failure.
   */
  public function load($id = null) {

    /*
     * If the id is null or is not an integer check if the id is set in the model
     * and reload the data based on the current id.
     */
    $id = !is_numeric($id) ? $this->getPrimaryKey() : $id;

    // If the id is still invalid, return a reference to the current object.
    if(!is_numeric($id)) {

      // Create an error message.
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Invalid Primary Key supplied.'
                   ], true);

      return $this;
    }

    //load the record from the database and store the results in the private row property.
    $sql = "SELECT * FROM " . $this->_tableName;
    $sql .= " WHERE " . $this->getPrimaryKeyName() . "=" . $id;

    // Store the sql string inside the class property.
    $this->_sql = $sql;

    //$result = $this->_database->execute($sql);
    $result = $this->exec(true);
    return $this;
  }

  /*
   * @method id() - returns the primary key associated with the model (Alias for getPrimaryKey()).
   * @return Int - return the primary key.
   */
  public function id() {
    return $this->getPrimaryKey();
  }


  /*
   * @method save() - Saves the model data to the database and returns the results.
   * @param $reload - Set true to reload the data from the database after the insert/update operation.
   * @return Mixed - return the primary key.
   */
  public function save($reload = false) {
    /*
     * if the current entity_id field is null insert a new record else update.
     */
    $result = null;
    $valid = is_numeric($this->getPrimaryKey());
    if(!$valid) {
      $result = $this->insert()->exec() ? $this : false;
    } else if($valid) {
      $result = $this->update()->exec();
    }

    if($reload === true && $result) {
      //Reload the data.
      $this->load();
    }

    return $result;
  }

  /*
   *@method delete() - Delete the currently loaded model.
   *@return Boolean - returns true on success or false on failure.
   */
  public function populate($idField, $tablename, $fieldname) {
    
  }

  /*
   *@method delete() - Delete the currently loaded model.
   *@return Boolean - returns true on success or false on failure.
   */
  public function delete() {
    //validate the the entity_id is set and not empty.
    $valid = is_numeric($this->getPrimaryKey());
    if($valid && parent::delete()->exec()) {
      return $this->reset();
    } else {
      return false;
    }
  }

  public function deleteById($id) {
    if(is_numeric($id)) {
      return $this->set($this->getPrimaryKeyName(), $id)->delete();
    } else {
      #if invalid set an error message
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Invalid Primary Key value'
                   ], true);

      return false;
    }
  }

  /*
   * @method where() - Select a record using the specified where clause.
   * @param String  - query string.
   * @return Object - return the object pointed to by 'this'.
   */
  public function where($field, $op, $q = null, $params = [], $raw = false) {

    #validate the field supplied is in the current table schema
    if(!$this->isField($field)) {
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Field \'' . $field . '\' Is not part of the selected Schema'
                   ]);
      return null;
    }

    #if the op parameter is empty, set it to the value of q and default the op to an '='.
    if(is_null($q)) {
      $q = $op;
      $op = '=';
    }

    #validate the query string $q
    if(is_null($q) || empty($q)) {
      #if invalid set an error message
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Query String \'' . serialize($q) . '\' Is invalid'
                   ]);
       return null;
    }

    #collection object
    $collection = null;

    // Process the op.
    $op = strtolower($op);

    // Escape the query values.
    if(is_scalar($q)) {
      $q = $this->_database->escape_string($q);
    } else if( is_array($q)){
      foreach($q as $key => $value) {
        $q[$key] = $this->_database->escape_string($value);
      }
    }

    // Validate the 'op' operator and transform the 'q' to the appropriate format.
    switch ($op) {
      case '=':
        $q = '\'' . $q . '\'';
        break;
      case 'like':
        $q = "'%" . $q . "%'";
        break;
      case 'in':
        $temp = null;
        if(is_array($q)) {
          //Wrap each element in quotes
          $temp = "'" . implode("','", $q) . "'";
        } else if (is_scalar($q)) {
          $temp = $q;
        }
        $q = '(' . $temp . ')';
        break;
      default:
        $this->error([
                     __CLASS__,
                     __METHOD__,
                     'Operator \'' . $op . '\' Is invalid or not supported.'
                    ]);
        return null;
    }

    #build the select query.
    $this->_sql = "SELECT * FROM " . $this->_tableName .
      " WHERE " . $this->_tableName . '.' . $field . ' ' . $op . ' ' . $q;

    // Process parameters.
    if(isset($params['orderby'])) {
      $this->_sql .= ' ORDER BY ' . $params['orderby']['field'] . ' ' . $params['orderby']['order'] ;
    }
    if(isset($params['limit']) && is_numeric($params['limit'])) {
      $this->_sql .= ' LIMIT ' . intval($params['limit'], 10);
    }
    if(isset($params['offset']) && is_numeric($params['offset'])) {
      $this->_sql .= ' OFFSET ' . intval($params['offset'], 10);
    }
    #execute the query and build a collection.
    $result = $this->exec(true);

    return !$raw ? $this->buildCollection($result) : $result;
  }

  public function match($field, $q, $params = [], $raw = false) {
    return $this->where($field, 'like', $q, $params, $raw);
  }

  /*
   * @method all() - returns an array with all records in the table.
   * @return Array - returns array..
   */
  public function all($raw = false) {
    $this->_sql = "SELECT * FROM " . $this->_tableName;

    $result = $this->exec(true);
    return !$raw ? $this->buildCollection($result) : $result;
  }

  private function buildCollection($data) {
    return new Collection([
                            'tablename' => $this->_tableName,
                            'schema'    => $this->_schema,
                            'database'  => $this->_database], $data);
  }
}

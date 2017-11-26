<?php

class Field extends _database_base {
  //public properties

  //private properties
  //field properties - database field properties
  private $_field_name    = null;
  private $_mysql_type    = null;
  private $_allow_null    = null;
  private $_key           = null;
  private $_default_value = null;
  private $_extra         = null;

  //field _value
  private $_value = null;
  //flag that is set if the _value is a function call instead of a static _value.
  private $_value_is_function = false;

  //_type field parsed and the max number of characters _extracted.
  private $_type           = null;
  private $_max_length     = null;
  private $_precision      = null;
  private $_allowed_values = array();  //array holds preset _values for the enum and set _types.

  //_extra fields
  private $_alias = null;  //_alias of the field name, if desired to be different then the default field.

  public function __construct($field = null) {
    if(is_array($field)) {
      $this->setName($field[ 'Field' ]);
      $this->setMySQLType($field[ 'Type' ]);
      $this->setAllowNull($field[ 'Null' ]);
      $this->setKey($field[ 'Key' ]);
      $this->setDefault($field[ 'Default' ]);
      $this->setExtra($field[ 'Extra' ]);
    }

  }

  //magic __toString() method will return a string with all of the fields.
  public function __toString() {

  }

  public function __set($name, $value) {
    $this->setValue($value);
  }

  public function __get($value) {
    return $this->getValue();
  }

  public function setName($value) {
    $this->_field_name = $value;
  }

  public function getName() {
    return $this->_field_name;
  }

  public function setMySQLType($value) {
    $this->_mysql_type = $value;
    //set the mysql _type _value and parse it into th elength and _type _value for php.
    $this->formatMySQLFieldType();
  }

  public function getMySQLType() {
    return $this->_mysql_type;
  }

  public function setAllowNull($value) {
    $this->_allow_null = $value;
  }

  public function getAllowNull() {
    return $this->_allow_null;
  }

  public function setKey($value) {
    $this->_key = $value;
  }

  public function getKey() {
    return $this->_key;
  }

  public function setDefault($value) {
    $this->_default_value = $value;

    //if the _mysql_type is a timestamp, and the default _value is current_timestamp
    //set the _value_is_function flag to true
    if(strtolower($this->_mysql_type) == 'timestamp' && strtolower($value) == 'current_timestamp') {
      $this->_value_is_function = true;
    }
  }

  public function setExtra($value) {
    $this->_extra = $value;
  }
  //can uset he __set and __get methods;
  /*
   *@method string setValue($value, $is_func) - sets the $value property to the $value passed to the function, and if the $is_func is not null sets that.
   *@param string $value - the _value to set inside the private $value property.
   *@param boolean $is_func - if its not null, set the _value_is_function property to the _value of $is_func.
   */
  public function setValue($value, $is_func = null) {

    //check if the _value is a string or a number
    if(!empty($value) && !is_scalar($value)) {
      //set the error and return false
      $err = "generic_database_field.setValue(): Could not set _value, invalid _type.";
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     $err
                   ]);

      //return false;
      return false;
    }
    //set the _value.
    $this->_value = $value;
    //check if the $is_funk _value is not null and set the private property
    if(!is_null($is_func) && is_bool($is_func)) {
      $this->_value_is_function = $is_func;
    }

    //return true if everything was successful
    return true;
  }

  /*
   *@method isFlag($name) - retruns true if the flag is set.
   *@param string $name - name of the flag.
   *@return boolean - returns true if flag is set. False otherwise.
   */
  public function isFlag($name = '') {
    //validate the flag name supplied
    if(empty($name) || !is_scalar($name)) {
      return false;
    }
    if(is_array($this->_flags) && isset($this->_flags[ $name ]) &&
      !empty($this->_flags[ $name ]) && is_scalar($this->_flags[ $name ])
    ) {
      return true;
    } else {
      return false;
    }
  }

  /*
   *@method getFlag() - Retrieves the supplied flags _value.
   *@param string $name - Name of the flag
   */
  public function getFlag($name = '') {
    //validate the flag name supplied
    if(empty($name) || !is_string($name)) {
      return null;
    }
    if(is_array($this->_flags) && isset($this->_flags[ $name ]) &&
      !empty($this->_flags[ $name ]) && is_scalar($this->_flags[ $name ]) || is_bool($this->_flags[ $name ])
    ) {
      return $this->_flags[ $name ];
    } else {
      return false;
    }
  }

  public function getValue() {
    if(is_null($this->_value) || (empty($this->_value) && !is_numeric($this->_value))) {
      return null;
    }
    #unescape the string.
    return stripslashes($this->_value);
  }

  public function getInsertValue() {
    $value = $this->getValue();
    // Check if the value is null, try to get the default value.
    if(is_null($value)) {
      $value = $this->getDefaultValue();
    }
    // If the value is null, set it explicitly to NULL.
    $value = is_null($value) ? 'NULL' : $value;

    $value = $this->isFunction() || $value === 'NULL' ? $value : "'" . $value . "'";

    return $value;
  }


  /*
   *@method bool isFunction() - returns true if the set _value is a function call instead of a static _value.
   *@param bool $flag - the flag is used to set the _value_is_function property, if the flag is empty, it returns the current _value.
   *@return boolean - returns true if the _value currently set is meant to be a mysql function call. else return false for normal _value
   */
  public function isFunction($flag = null) {
    //if the flag is null, return the current _value.
    if(is_null($flag)) {
      return $this->_value_is_function;
    }
    //else set the _value_is_function property to the flag.
    if(!is_null($flag) && is_bool($flag)) {
      $this->_value_is_function = $flag;
    }
  }

  public function __destruct() {

  }

  public function getMaxLength() {
    if(isset($this->_max_length) && !empty($this->_max_length)) {
      return $this->_max_length;
    } else {
      return null;
    }
  }

  public function getType() {
    if(isset($this->_type) && !empty($this->_type)) {
      return $this->_type;
    } else {
      return false;
    }
  }

  public function getAllowedValues() {
    return $this->_allowed_values;
  }

  /*
   *@method string getDefaultValue() - returns the default _value for the field is one is set, or and empty string.
   *@return string - returns the _value if one is set or an empty string.
   */
  public function getDefaultValue() {
    return empty($this->_default_value) &&
            !is_numeric($this->_default_value) ? null : $this->_default_value;
  }

  //returns the properties of the field in an array, if $associative paramerter is true, the array will be an
  //associative array.
  public function get_field_properties() {
    $properties = array();

    return $properties;
  }
  //the field vill validate the data in its _value property versus the length and _type properties and
  //dispaly appropriate _errors.
  //Returns true or False.
  public function validate() {
    //if the field _type is the primary _key and the _extra auto_increment is set, return true.
    if(strtolower($this->_key) == 'pri' && strtolower($this->_extra) == 'auto_increment') {
      return true;
    }

    /*
     *validate that the value is not null and not a number 0 if the field does not allow null.
     * 0 will return true for the empty() check therefore it must be specifically checked to not
     * equal 0 both in type and value.
     */

    if(!$this->allowNull() && (is_null($this->_value) || empty($this->_value) &&
        $this->_value !== '0') && !$this->hasDefault()
    ) {
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     "'$this->_field_name' is Required"
                   ]);

      return false;
    } elseif(!empty($this->_value) && !$this->isValidType()) {
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     "'$this->_field_name' is Invalid"
                   ]);

      return false;
    } elseif(!empty($this->_value) && !$this->isValidLength()) {
      $this->error([
                     __CLASS__,
                     __METHOD__,
                     "'$this->_field_name' is to long(Max:" . $this->_max_length . " | Current:" . strlen($this->_value) . ")"
                   ]);
    } else {
      return true;
    }

  }

  /*
   * @method hasDefault() - Checks if the DB field has a default value.
   */
  public function hasDefault() {
    return !empty($this->_default_value) || $this->_default_value === '0';
  }

  //will return yes or no based on if null is allowed or not.
  public function allowNull() {
    if(strcmp(strtolower($this->_allow_null), strtolower('yes')) == 0) {
      return true;
    } else {
      return false;
    }
  }

  public function isValidType() {

    switch($this->_type) {
      //numeric _types
      case 'bit':
        $is_valid = 'is_numeric';
        break;
      case 'tinyint':
        $is_valid = 'is_numeric';
        break;
      case 'smallint':
        $is_valid = 'is_numeric';
        break;
      case 'mediumint':
        $is_valid = 'is_numeric';
        break;
      case 'int':
        $is_valid = 'is_numeric';
        break;
      case 'bigint':
        $is_valid = 'is_numeric';
        break;
      case 'float':
        $is_valid = 'is_float';
        break;
      case 'double':
        $is_valid = 'is_double';
        break;
      case 'decimal':
        $is_valid = 'is_double';
        break;


      //string _types
      case 'char':
        $is_valid = 'is_string';
        break;
      case 'varchar':
        $is_valid = 'is_string';
        break;
      case 'tinytext':
        $is_valid = 'is_string';
        break;
      case 'text':
        $is_valid = 'is_string';
        break;
      case 'mediumtext':
        $is_valid = 'is_string';
        break;
      case 'longtext':
        $is_valid = 'is_string';
        break;
      case 'enum':
        $is_valid = 'is_string';
        break;
      case 'set':
        $is_valid = 'is_string';
        break;
      //blob _types
      case 'binary':
        $is_valid = 'is_string';
        break;
      case 'varbinary':
        $is_valid = 'is_string';
        break;
      case 'tinyblob':
        $is_valid = 'is_string';
        break;
      case 'blob':
        $is_valid = 'is_string';
        break;
      case 'mediumblob':
        $is_valid = 'is_string';
        break;
      case 'longblob':
        $is_valid = 'is_string';
        break;


      //dates
      case 'date':
        $is_valid = 'is_string';
        break;
      case 'datetime':
        $is_valid = 'is_string';
        break;
      case 'time':
        $is_valid = 'is_string';
        break;
      case 'timestamp':
        $is_valid = 'is_string';
        break;
      case 'year':
        $is_valid = 'is_string';
        break;

      default:
        return false;
    }

    return $is_valid($this->_value);
  }

  public function isValidLength() {
    //if enum or set, check the allowed _values insted of max length
    switch($this->_type) {
      case 'enum':
      case 'set':
        if(in_array($this->_value, $this->_allowed_values)) {
          return true;
        } else {
          return false;
        }
        break;
      case 'varchar':
        //Issue a warning if the length is greater since the value will be truncated.
        if(strlen($this->_value) > $this->_max_length) {
          $this->warning([__CLASS__,
                          __METHOD__,
                          $this->_field_name . ' - length exceeded, data will be truncated to: ' . $this->_max_length
                         ]);
        }
        break;
      case 'date':
      case 'datetime':
      case 'time':
      case 'timestamp':
        strlen($this->_value) > 0;
        break;

      default:
        if(strlen($this->_value) > $this->_max_length) {
          return false;
        }
        break;

    }

    return true;
  }

  /*
   *@method isPrimaryKey() - returns true of the current field is the primary _key.
   *@return boolean - returns true if the current field is the primary _key.
   */
  public function isPrimaryKey() {
    return $this->_key == "PRI" ? true : false;
  }

  /*
   *@method clearValue() - sets the _value property to a null _value
   */
  public function clearValue() {
    $this->_value = null;

    return true;
  }

  //private properties

  //will split the _type field and _extract the number of the maximum allowed characters.
  private function formatMySQLFieldType() {

    $pattern = '/[\(\),]/';
    $parts   = preg_split($pattern, $this->_mysql_type);

    $this->setType($parts[ 0 ]);


    //set up a switch statement that tests for the text _type _values and assigns the max to the
    //max _value property
    switch($parts[ 0 ]) {
      case 'tinytext':
        $this->_max_length = 256;

        return;
        break;
      case 'text':
        $this->_max_length = 65535;

        return;
        break;
      case 'mediumtext':
        $this->_max_length = 16777215;

        return;
        break;
      case 'longtext':
        $this->_max_length = 4294967295;

        return;
        break;
      case 'enum':
        //set the allowed _values.
        $this->setAllowedValues(array_slice($parts, 1));

        return;
        break;
      case 'set':
        //set the allowed _values.
        $this->setAllowedValues(array_slice($parts, 1));

        return;
        break;
      case 'timestamp':
        //set the max size of a timestamp.
        $this->_max_length = 20;
      case 'datetime':
        //set the max size of a datetime field.
        $this->_max_length = 20;
    }
    //if _types are numeric and have _precision set the length
    if(isset($parts[ 1 ]) && !empty($parts[ 1 ])) {
      $this->setMaxLength($parts[ 1 ]);
    }
    //set the _precision
    if(isset($parts[ 2 ]) && !empty($parts[ 2 ])) {
      $this->setPrecision($parts[ 2 ]);
    }
  }

  private function setMaxLength($value) {

    if(is_numeric(trim($value))) {
      $this->_max_length = (int)$value;
    } else {
      throw new Exception('Failed max length, value must be numeric.');
      exit;
    }
  }

  private function setType($value) {
    if(!empty($value)) {
      $this->_type = strtolower(trim($value));
    } else {
      throw new Exception('Failed to set Field Type, value is required.');
    }
  }

  private function setPrecision($value) {
    if(!empty($value)) {
      $this->_precision = (int)trim($value);
    } else {
      throw new Exception('Failed to set Precision, value is required.');
    }
  }

  private function setAllowedValues($parts) {
    // $this->_allowed_values = array_merge($this->_allowed_values, $parts);
    foreach($parts as $part) {
      if(!empty($part)) {
        $string = str_split($part);
        if($string[ 0 ] == "'" && $string[ count($string) - 1 ] == "'") {
          $part = array();
          for($i = 1; $i < count($string) - 1; $i++) {
            $part[] = $string[ $i ];
          }
          $this->_allowed_values[] = implode($part);
        } else {
          $this->_allowed_values[] = $part;
        }

      }
    }
  }
}

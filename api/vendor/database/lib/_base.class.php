<?php
/**
 * Created by PhpStorm.
 * User: Nazar Lesiv
 * Date: 5/2/2015
 * Time: 8:37 PM
 */

class _database_base {

  //valid flag
  protected $_valid = true;
  protected $_log = [
    'error' => [],
    'warning' => [],
    'message' => []
  ];

  protected $_errors = array();
  protected $_messages = array();
  protected $_warnings = array();

  public function __construct($args = array()) {

    /*
    *loop thought the args associative array and call the setter method
    *for each key value in the array.
    */

    if(is_array($args) && !empty($args)) {

      foreach($args as $key => $value) {

        #check if the key is a valid string.
        if(!is_string($key)) { continue; }

        #build the method name string
        $method = 'set' . $key;

        #if the method exists, call it and pass the value.
        if(method_exists($this, $method)) {
          $this->$method($value);
        }
      }
    }
  }
  private function log($type, $msg, $throw = false) {
    if(isset($this->_log[$type])) {
      $this->_log[$type][] = $msg;
    }
    #if $throw is true, throw and Exception and flatten the message.
    $msg = $this->flatten($msg, '->');
    if($throw === true) {
      throw new Exception($msg);
      exit;
    }
  }

  private function flatten($input, $glue = '') {
    $str = '';
    if(is_string($input)) {
      $str .= $input;
      return $str;
    }

    if(is_array($input) && !empty($input)) {
      foreach($input as $key => $value) {
        $str .= $glue . $this->flatten($value) ;
      }
    }

    return '[' . $str . ']';

  }
  private function getLog($type) {
    return isset($this->_log[$type]) ? $this->_log[$type] : [];
  }
  protected function warning(Array $warn, $throw = false) {
    return $this->log('warning', $warn, $throw);

  }
  protected function error(Array $err, $throw = false) {
    return $this->log('error', $err, $throw);
  }
  protected function message(Array $msg, $throw = false) {
    return $this->log('message', $msg, $throw);
  }

  public function getErrors() {
    return $this->getLog('error');
  }

  public function hasErrors() {
    return isset($this->_log['error']) && !empty($this->_log['error']);
  }

  /*
   *@method clearErrors() - sets the _errors array to an empty array.
   */
  public function clearErrors() {
    $this->_errors = array();
    return true;
  }
}


// Common Traits.

/*
 * @trait middleware - Trait that will apply middlware to database models.
 */
trait middleware {
  private $_middlware_condition = '';
  private $_middlware_action = '';

  /*
   * middleware hooks that will be applied to all models.
   * anything registered in the database object middleware will cascade to every model.
   */
  private $_middleware  = [];


  /*
   * @method pre() - Assigns a 'pre' action handler to be execute before the specified action.
   * @param String $a - Specified action.
   * @param Callable $h - Handler function.
   * @return Object $this - Object $this.
   */
  public function pre($a, $h = null) {
    $this->_middlware_condition = 'pre';
    $this->_middlware_action = $a;
    $this->middleware($h);
    return $this;
  }

  /*
   * @method post() - Assigns a 'post' action handler to be execute after the specified action.
   * @param String $a - Specified action.
   * @param Callable $h - Handler function.
   * @return Object $this - returns Object $this.
   */
  public function post($a, $h = null) {
    $this->_middlware_condition = 'post';
    $this->_middlware_action = $a;

    $this->middleware($h);
    return $this;
  }

  /*
   * @method clear() - Assigns a 'post' action handler to be execute after the specified action.
   * @param Number $i - Position at which to remove the item.
   * @return Object $this - returns Object $this.
   */
  public function clear($i = null) {
    if(isset($this->_middleware[$this->_middlware_condition]) &&
    isset($this->_middleware[$this->_middlware_condition][$this->_middlware_action])) {

      // If an index $i is supplied, try to remove just the handler as the specified location.
      if(is_numeric($i) && $this->_middleware[$this->_middlware_condition][$this->_middlware_action][$i]) {
        unset($this->_middleware[$this->_middlware_condition][$this->_middlware_action]);
      } else {
        // Unset all of the handlers for the specified action.
        unset($this->_middleware[$this->_middlware_condition][$this->_middlware_action]);
      }
    }
    return $this;
  }

  /*
   * @method post() - Assigns a 'post' action handler to be execute after the specified action.
   * @param String $c - 'pre' or 'post' condition identifier.
   * @param String $a - Specified action.
   * @param Callable $h - Handler function.
   * @return Boolean - returns true on success or false on railure.
   */
  private function middleware($h) {
    if(!$this->_middlware_action) { return false; }

    $a = $this->_middlware_action;
    $c = $this->_middlware_condition;

    // Check if the Condition key exists.
    if(!isset($this->_middleware[$c])) {
      $this->_middleware[$c] = [];
    }

    if(!isset($this->_middleware[$c][$a])) {
      $this->_middleware[$c][$a] = [$h];
    } else {
      $this->_middleware[$c][$a][] = $h;
    }

    return true;
  }

  /*
   * @method runMiddlware - Execute the middleare assigned based on the condition and the action.
   * @param String $c - Condition statement, 'pre' or 'post'.
   * @param String $a - Action statement, 'insert', 'delete', 'select', 'update'.
   * @return Boolean - true on success or false on failure.
   */
  private function runMiddlware($c, $a) {
    if(isset($this->_middleware[$c]) && isset($this->_middleware[$c][$a])) {
      $handlers = $this->_middleware[$c][$a];
      $count = count($handlers);

      // Capture the results from the handler calls into an array.
      $results = [];
      for($i = 0; $i < $count; ++$i) {
        if(is_callable($handlers[$i])) {
          $results[] = $handlers[$i]($this->getDatabase(), $this, $results);
        }
      }
    }

    return;
  }

  /*
   * @method set_middleware - Sets the arguments array to the model's middleware property. This is tipically only
   *  used by the constructor to set global middleware handlers that were applied to the database factory object
   *  that created the current model.
   * @param Array $args - assocciative array of conditions, actions, and handlers for each action.
   * @return Mixed;
   */
  protected function set_middleware(Array $args) {
    return $this->_middleware = $args;
  }
}

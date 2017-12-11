<?php
/*
 *The request class.
 */

class Request {

  /*
   *store the parsed header objects.
   */
  private $_contentType = null;

  /*
   * flag to specify if the call was via ajax.
   */
  private $_ajax = false;

  /*
   *store the request method.
   */
  private $_method = null;

  private $_debug = 0;
  private $_callstack = [];

  public function __construct($config = null) {
    //extract the properties from the global arrays.
    //request method.
    $this->_method = $_SERVER['REQUEST_METHOD'];

    if(!$this->isGet() && strtolower($this->getContentType()) === 'application/json') {
      //set the _ajax flag to true
      $this->_ajax = true;
      //set the request object to the parsed request array.
      $_REQUEST = $this->parseJson();

    }

    $this->_debug = 0;
  }

  public function __destruct()
  {
    if($this->_debug) {
      $this->_callstack[] = __FUNCTION__;
      var_dump($this->_callstack);
    }
  }

  public function __set($name, $value) {

  }
  public function __get($name) {

  }

  public function __toString() {
    return serialize($this);
  }
  /*
   *@method getParam() - gets the value of the specified key from the request.
   *@return mixed - returns the values of the specified key, or null on failure.
   */
  public function get($key = '') {

    //split the key on / to drill down for grouped request data.
    $parts = $this->parse($key);

    //temporary container.
    //set initialy to the $_REQUEST array.
    $temp = $_REQUEST;

    //drill down the grouped values in the request array.
    foreach($parts as $key) {

      //check if the key exists in the temp array.
      //if so assign that value to temp.
      if(isset($temp[$key])) {
        $temp = $temp[$key];
      } else {

        //if the key does not exists inside the temp container,
        //return null.
        return null;
      }
    }

    //return the temp value.
    return $temp;

    /*
    return isset($_REQUEST[$key]) && !empty($_REQUEST[$key]) ?
        $_REQUEST[$key] : null;
    */
  }

  //returns all of the parameters as an associative array.
  public function raw() {
    return $_REQUEST;
  }

  //get data submitted as json
  public function parseJson() {
    return json_decode(file_get_contents('php://input'),true);
  }
  //return true of request method is post, false otherwise.
  public function isPost() {
    return $this->method() == 'post';

  }

  //return true if request method is get, false otherwise.
  public function isGet() {
    return $this->method() == 'get';
  }

  /*
   *@methed method - returns the request method.
   *@return String - Returns the request method name as string.
   */
  public function method() {
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  //return true if the request is an ajax call. false otherwiese.
  public function isAjax() {
    /*
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    */
    return $this->_ajax || isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }


  public function getQuery() {
    return isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ?
      $_SERVER['QUERY_STRING'] : null;

  }

  public function getReqUri() {
    if($this->_debug) {
      $this->_callstack[] = __FUNCTION__ . ': ' . implode(func_get_args());
    }
    return isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ?
      $_SERVER['REQUEST_URI'] : null;
  }

  public function getUrl() {
    if($this->_debug) {
      $this->_callstack[] = __FUNCTION__ . ': ' . implode(func_get_args());
    }
    return isset($_SERVER['URL']) && !empty($_SERVER['URL']) ?
      $_SERVER['URL'] : null;
  }

  /************************HEADER MANIPULATION METHODS**************************/
  /*
   * @method parse() - parses the path string into an array of nodes.
   * @param String $path - String path delimited by a . or a /
   */
  public function parse($path) {
    if(!is_string($path) || empty($path)) { return array(); }
    return preg_split('/[\.\/]/', $path);
  }

  public function getContentType() {
    #Check if a content type exists, if not rent the basic text/plain.
    #This is likely the case for get requests.
    if(!isset($_SERVER['CONTENT_TYPE'])) { return 'text/plain'; }

    $type = explode(';', $_SERVER['CONTENT_TYPE']);

    #default to 'text/plain'
    return $this->_contentType = count($type) > 0 ? trim($type[0]) : 'text/plain';
  }

  public function getAcceptHeaders() {
    //If an HTTP_ACCETP header is not supplied, return empty array.
    if (!isset($_SERVER['HTTP_ACCEPT'])) {
      return [];
    }

    $header = $_SERVER['HTTP_ACCEPT'];
    $values = preg_split('/\s*,\s*/', $header);

    //Todo : Sort header values based on q values.

    $data_arr = array();
    foreach($values as $val) {
      $data_arr[$val] = $val;
    }

    return $data_arr;
  }
}

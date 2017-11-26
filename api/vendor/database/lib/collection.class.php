<?php

/*
 *collection class
 */

class Collection
  extends _database_base
  implements \Iterator {

  #array to store the collection of models built for the query resuts.
  private $_collection = array();


  public function __construct($config, $data) {

    if(is_array($data) && is_array($config)) {
      $count = count($data);
      for($i = 0; $i < $count; $i++) {
        $model = new Model($config);
        $model->setValues($data[$i]);
        $this->_collection[] = $model;
      }
    }
  }

  public function count() {
    return count($this->_collection);
  }

  public function toArray() {
    $output = [];
    if(is_array($this->_collection)) {
      $count = count($this->_collection);
      for($i = 0; $i < $count; $i++) {
        $output[] = $this->_collection[$i]->toArray();
      }
    }
    return $output;
  }

  public function isEmpty() {
    return !$this->count();
  }

  public function first() {
    return isset($this->_collection[0]) ? $this->_collection[0] : null;
  }

  public function last() {
    $last = $this->count() - 1;
    return isset($this->_collection[$last]) ? $this->_collection[$last] : null;
  }

  public function get($i) {
    if(!is_integer($i)) { return null; }
    return isset($this->_collection[$i]) ? $this->_collection[$i] : null;

  }

  public function findOne($name, $op, $value = null) {
    $res = $this->where($name, $op, $value);
    if(is_array($res) && !empty($res)) {
      return $res[0];
    }

    return null;
  }

  public function where($name, $op, $value = null) {
    if(empty($this->_collection) || !is_string($name) || !is_string($op)) {
      return null;
    }

    //If the value is not supplied, default the operator to ===
    if(!$value) {
      $value = $op;
      $op = '=';
    }

    $results = [];

    foreach($this->_collection as $key => $model) {
      switch ($op) {
        case '=':
          if($model->get($name) === $value) {
            $results[] = $model;
            continue;
          }
          break;

        default:
          # code...
          break;
      }
    }

    return $results;
  }

  /*******************************ITERATOR ABSTRACT METHODS********************/

  public function current() {
    return current($this->_collection);

  }

  public function key() {
    return key($this->_collection);
  }

  public function next() {
    return next($this->_collection);
  }

  public function rewind() {
    return reset($this->_collection);

  }

  public function valid() {
    return key($this->_collection) !== null;
  }
}

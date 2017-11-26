<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/13/16
 * Time: 7:16 PM
 */
class Query
{
  private $_db = null;

  private $_nodes = [
    'select'   => [],
    'from'     => [],
    'where'    => [],
    'order by' => [],
    'limit'    => []
  ];

  private $_sql = null;

  public function __construct($db)
  {
    $this->_db = $db;
  }

  #Alian formatter. EX: 'DateUpdate as date'
  private function _a($f, $alias = null)
  {
    return is_string($alias) ? $f . ' as ' . $alias : $f;
  }

  #value formatted. Escapes and formats sql values.
  private function _v($v)
  {
    return '\'' . $this->_db->escape($v) . '\'';
  }

  #comparison formatter. EX: 'where table.fieldName = 2'
  /*
   * @param $f - fieldname
   * @param $o - comparison operator
   * $param $v - value
   */
  private function _c($f, $o, $v)
  {
    //Todo: Switch case for possible comparison operators.

    return $f . $o . $this->_v($v);
  }

  public function select($f, $alias = null)
  {

    if (is_array($f)) {
      $count = count($f);
      for ($i = 0; $i < $count; ++$i) {
        if (is_string($f[ $i ])) {
          $this->_nodes['select'][] = $this->_a($f[ $i ], $alias);
        } else if (is_array($f[ $i ]) && count($f[ $i ]) === 2) {
          $this->_nodes['select'][] = $this->_a($f[ $i ][0], $f[ $i ][1]);
        }
      }

    } else if (is_string($f)) {
      $this->_nodes['select'][] = $this->_a($f, $alias);
    }
    return $this;
  }

  public function from($t, $alias = null)
  {
    $this->_nodes['from'][] = $this->_a($t, $alias);

    return $this;
  }

  public function join($t, $alias = null)
  {
    $this->_nodes['from'][] = 'join ' . $this->_a($t, $alias);

    return $this;
  }

  public function leftJoin($t, $alias = null)
  {
    $this->_nodes['from'][] = 'left join ' . $this->_a($t, $alias);

    return $this;
  }

  public function rightJoin($t, $alias = null)
  {
    $this->_nodes['from'][] = 'right join ' . $this->_a($t, $alias);

    return $this;
  }

  public function on($f1, $f2)
  {
    $this->_nodes['from'][] = 'on ' . $f1 . '=' . $f2;

    return $this;
  }

  public function where($f, $o, $v)
  {
    //Todo: escape the values.
    $this->_nodes['where'][] = $f . $o . $this->_v($v);

    return $this;
  }

  public function orderBy($f, $o)
  {
    $this->_nodes['order by'][] = $f . ' ' . $o;
    return $this;

  }

  public function groupBy($f, $o)
  {


    return $this;

  }

  /**
   * @param $c Integer - Number of records to return(count).
   * @param $o Integer - Starting at this value(offset).
   * @return Object - Returns $this.
   */
  public function limit($c, $o = null)
  {
    if (!is_numeric ($c)) {
      return $this;
    }
    $this->_nodes['limit'][] = !is_null($o) && is_numeric ($o) ? $o . ',' . $c : $c;
  }

  public function _and($f, $o, $v)
  {
    //Todo: escape the values.
    $this->_nodes['where'][] = 'AND ' . $this->_c($f, $o, $v);

    return $this;
  }

  public function _or($f, $o, $v)
  {
//Todo: escape the values.
    $this->_nodes['where'][] = 'OR ' . $this->_c($f, $o, $v);

    return $this;
  }

  public function not($value)
  {

    return $this;

  }

  public function sql()
  {
    return $this->build();
  }
  public function __toString() {
    return $this->build();
  }

  private function build()
  {
    #build the query and return.
    $sql = '';
    foreach ($this->_nodes as $key => $value) {
      #Of the clause has no values continue
      if (empty($value)) {
        continue;
      }
      $sql .= ' ' . $key . ' ';
      switch ($key) {
        case 'select':
          $sql .= implode(',', $value);
          break;
        default:
          $sql .= implode(' ', $value);
          break;
      }
      /*
      $count = count($value);
      for($i = 0; $i < $count; ++$i) {
        $sql .= ' ' . $value[$i];
      }
      */
    }
    $this->_sql = $sql;
    return $sql;
  }
}

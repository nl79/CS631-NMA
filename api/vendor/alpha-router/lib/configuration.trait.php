<?php
namespace alpha_router;
/*
 * @trait configuation - Trait that will configuation capabilities to core classes.
 */
trait configuration {
  /*
   * $_config key value map.
   */
  private $_config  = [];

  public function configure($config) {

    if(!is_array($config) || empty($config)) { return; }
    //Dynamic Setters
    foreach ($config as $key => $value) {
      //check if the key is a valid string.
      $this->setConfig($key, $value);
    }
  }

  /*
   * @method getConfig() - Gets a configuation value.
   * @param String $key - 'pre' or 'post' condition identifier.
   * @return Mixed - returns the configuation value or null on failure.
   */
  public function getConfig($key) {
    return isset($this->_config[$key]) ? $this->_config[$key] : null;
  }

  /*
   * @method setConfig() - Sets a configuation value.
   * @param String $key - configuation name.
   * @param String $value - configuation value.
   * @return Boolean - returns true on success or false on railure.
   */
  public function setConfig($key, $value) {
    if (!is_string($key)) {
      return false;
    }
    $this->_config[$key] = $value;
    return $this;
  }
}

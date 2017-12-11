<?php
namespace alpha_router;

trait hooks {

  // pre and post Hooks for certain methods.
  private $_hooks = [
    'before' => [],
    'after' => []
  ];

  public function before($event, $handler) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $this->_hooks['before'][$event] = $handler;
  }

  public function after($event, $handler) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $this->_hooks['after'][$event] = $handler;
  }

  private function getHook($condition, $caller) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    // Convert to lowercase.
    $condition = strtolower($condition);
    $caller = strtolower($caller);

    // If Exists return the function.
    return isset($this->_hooks[$condition][$caller]) &&
           is_callable($this->_hooks[$condition][$caller]) ?
           $this->_hooks[$condition][$caller] :
           null;
  }

}

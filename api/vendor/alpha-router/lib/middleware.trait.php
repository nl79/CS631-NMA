<?php
namespace alpha_router;

trait middleware {
  //middlewear handlers
  private $_middleware = [];

  // Executed Middleware functions.
  private $_calledMiddleware = [];

  public function getCalledMiddleware() {
    return $this->_calledMiddleware;
  }

  //Mount a middleware method at the specified mount point.
  //If the mount point is not specified, set it to root, which will always call the handler.
  public function middleware($mount, callable $handler = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $path = null;
    $fn   = null;

    //If Only 1 parameter is supplied, and it is a callable function, set it to the root mount point.
    if(is_callable($mount) && is_null($handler)) {
      $path = '/';
      $fn   = $mount;
    } else if(is_string($mount) && is_callable($handler)) {
      $path = $this->toRegex($mount);
      $fn   = $handler;
    } else {
      throw new Exception('Invalid argument supplied to: ' . __METHOD__);

      return;
    }

    // Split the mount path string on the / and count the nodes.
    $count = $this->nodeCount($path);
    if(!isset($this->_middleware[$count])) {
      $this->_middleware[$count] = [];
    }

    if(!isset($this->_middleware[$count][$path])) {
      $this->_middleware[$count][$path] = [$fn];
    } else {
      $this->_middleware[$count][$path][] = $fn;
    }
    return $this;
  }

  /*
  * @access public
  * @method getMiddleware() - Returns an array of middleware functions that would be applied
    to a given pattern.
  * @param String $pattern -  Pattern to match agains.
  * @param Boolean $exact - If set to true, only the middleware set to the exact URI will be returned.
  * @return Array - Returns an Array of Callable middleware functions
  *  added to the router. If the $mount point is supplied only the results attached
  *  so the specific point are returned.
  */
  public function getMiddleware($pattern = '', $exact = false) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    // Convert to regex expression and reduce the list based on pattern.
    return $this->_reduceMiddleware($this->toRegex($pattern));

  }

  private function _reduceMiddleware($pattern) {
    $collection = [];
    /*
     * If no pattern is supplied do not return results.
     * a root mount point of '/' should pass this validation
     * and be parsed to a count of 0 nodes, which would return
     * all middleware.
     */

    if(empty($this->_middleware) || !is_scalar($pattern)) {
      return $collection;
    }

    // If the pattern is empty, return all middleware.
    if(empty($pattern)) {
      foreach($this->_middleware as $count) {
        foreach($count as $uri => $fn) {
          $ct = count($fn);
          for($i = 0; $i < $ct; ++$i) {
            $collection[] = $fn[$i];
          }
        }
      }
      return $collection;
    }

    // Split the pattern and count the nodes.
    $nodeCount = $this->nodeCount($pattern);

    //print_r(array_keys($this->_middleware));
    for($k = 0; $k <= $nodeCount; ++$k) {
      // If No middleware exists for the current count, continue to the next.
      if(!isset($this->_middleware[$k]) || empty($this->_middleware[$k])) {
        continue;
      }

      foreach($this->_middleware[$k] as $key => $fn) {
        //print_r($key);
        //Match the key to the _matchedUrl.
        if($this->leftMatch($key, $pattern)) {
          $count = count($fn);
          for($i = 0; $i < $count; ++$i) {
            $collection[] = $fn[ $i ];
          }
        }
      }
    }

    return $collection;
  }

  public function leftMatch($pattern, $string) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    return substr($string, 0, strlen($pattern)) === $pattern;
  }
}

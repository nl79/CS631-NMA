<?php

require_once('lib/configuration.trait.php');
require_once('lib/hooks.trait.php');
require_once('lib/middleware.trait.php');

/*
*Router Class
*/

class Router {
  /*
   *  Apply the traits.
   */
  use
    \alpha_router\configuration,
    \alpha_router\middleware,
    \alpha_router\hooks;

  private $_initialized = false;

  /****************DEBUG PROPERTIES**************/
  private $_callstack = [];
  private $_debug     = 0;
  /****************END DEBUG PROPERTIES**********/
  private $_error = [];

  // The Number of redirects executed by this router instance.
  private $_redirects = 0;
  private $_maxRedirects = 10;

  // Routing Table of routes and handlers loaded into the Router instance.
  private $_routingTable = [];

  // Flag the specifies if the routing table was loaded from the cache.
  private $_fromCache = false;

  // The Fallback Handler function.
  private $_fallback = null;

  // Executed Handler functions.
  private $_calledHandlers = [];

  // Loaded Route files.
  private $_loadedRouteFiles = [];
  private $_filename = null;

  // Default route file pattern used to match files.
  private $_routeFilePattern = '/(.*)route\.php$/i';

  //the root mount point for the url.
  private $_mountPoint = null;

  //Flag that specifies if the request has been complete.
  private $_resolved = false;

  public function __construct($config = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    //Setup Defalt configuration for the router.
    $this->_config = [
      // Flag that determines if script execution is terminated on a successful handler call.
      'exitonsucess' => true,
      'cache.filename' => 'router',
      'cache' => false
    ];

    $this->configure($config);

    //Set the debug level based on the $kernel setting.
    $this->_debug = $this->getConfig('debug');
    $this->init();
  }

  public function __call($name, $args) {
  }

  public function done() {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    return $this->_resolved;
  }

  public function getCalledHandlers() {
    return $this->_calledHandlers;
  }

  public function mount($val) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $this->_mountPoint = $val;

    return $this;
  }

  public function nodeCount($pattern) {
    $trimmed = trim($pattern, '/');
    return empty($trimmed) ? 0 : count(explode('/', $trimmed));
  }

  public function __toString() {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    return serialize($this);
  }

  public function __destruct() {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
      echo('<pre>');
      print_r($this->_callstack);
      echo('</pre>');
    }
    //check if caching and store the list.
    if(is_object($cache = $this->getConfig('cache')) && !$this->_fromCache) {
      if(method_exists($cache, 'set')) {
        /*
         * - First is the array of methods. [get, post, put, delete, etc..]
         *  - Each contains an array of node lengths. ['2', '4', '5', etc...]
         *    - Each contains an array of pattners and route definitions. (remove the 'handler')
         */
        foreach($this->_routingTable as $method => $nodeCount) {
          foreach($nodeCount as $count => $routes) {
            foreach($routes as $pattern => $route) {
              //Unset the Handler callback for serialization.
              unset($this->_routingTable[$method][$count][$pattern]['handler']);
            }
          }
        }
        //Check if Cashing and write to the cache.
        $cache->set($this->getConfig('cache.filename'), $this->_routingTable);
      }
    }
  }

  public function get($url, $handler) {
    return $this->setRoute('get', $url, $handler);
  }

  public function post($url, $handler) {
    return $this->setRoute('post', $url, $handler);
  }

  public function delete($url, $handler) {
    return $this->setRoute('delete', $url, $handler);
  }

  public function put($url, $handler) {
    return $this->setRoute('put', $url, $handler);
  }

  public function fallback($handler) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    if(is_callable($handler)) {
      $this->_fallback = $handler;
    }
    return $this;
  }

  //Match Any of the verbs to the same handler method.
  public function match(Array $methods, $url, $handler) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $ct = count($methods);
    if($ct === 0) {
      //todo - property handle the fact that bad data was supplied.
      return $this;
    }
    for($i = 0; $i < $ct; ++$i) {
      $this->setRoute(trim($methods[ $i ]), $url, $handler);
    }

    return $this;
  }

  private function buildRoutingTable($rebuild = false) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    // If not force rebuilding the table, try to load cached data.
    if(!$rebuild) {
      // Try to Rebuild from cache if cashing is enabled.
      $cache = $this->getConfig('cache');
      if(is_object($cache) && method_exists($cache, 'get')) {
        // Load the data from the cache
        $cachedTable = $cache->get($this->getConfig('cache.filename'));
        // If the caches table is empty set the _fromCache flag to false.
        if(!empty($cachedTable)) {
          // If the _routingTable is currently not empty, that means handlers have been
          // set outside of the cached route files. The current table and the cached data
          // need to be merged to retain the handler references.
          $this->_routingTable = array_replace_recursive($cachedTable, $this->_routingTable);
          $this->_fromCache = true;
        } else {
          $this->_fromCache = false;
        }
      }
    } else {
      $this->_fromCache = false;
      $this->_routingTable = [];
    }

    // If the routing table is not loaded from cache.
    // OR if the routingTable is empty. reload it.
    //NOTE: handlers were registered before the route() method is called,
    // the routing table will not be empty but will be incompleted because the
    // route files will not be loaded.
    if($this->_fromCache !== true || empty($this->_routingTable)) {

      // Reload all of the files.
      $this->loadFiles();
      // Set the front cache flag to false.
      $this->_fromCache = false;
    }

    //Set the app as initialized to true.
    $this->_initialized = true;
    return;
  }

  public function route($methodOrUrl, $url = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    // If the routing table has not been built, build the table.
    if($this->_initialized !== true) {
      $this->buildRoutingTable();
    }

    // If the URL is null, default the method to GET.
    if(empty($url) || !is_string($url)) {
      $method = 'get';
      $url = is_string($methodOrUrl) && !empty($methodOrUrl) ? $this->parseUrl($methodOrUrl) : null;
    } else {
      $method = $methodOrUrl;
    }

    $url = is_string($url) ? $this->parseUrl($url) : null;

    // Get the route data from the routing table.
    $route = $this->getRoute($method, $url);

    //check if a handler method has been assigned.
    if(isset($route['handler']) && !is_null($route['handler'])) {

      return $this->handleRequest($route);

    } else if(is_callable($this->_fallback)) {
      // Cache the fact that a fallback was used for this route.
      // This is mostly to prevent the router from loading all of the files
      // for repeated attempts to access a URL for which a route is not defined.
      //Build a route definition for the fallback route.
      $route = ['handler' => $this->_fallback,
                'pattern' => $url,
                'method' => $method];
      return $this->handleRequest($route);
    }

    //If no matches have been found, return a 404 error code.
    return false;
  }

  public function parseUrl($val) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    //if the query string is in the URI
    $i    = strpos($val, '?');
    $path = $i > 0 ? substr($val, 0, $i) : $val;

    //trim off the trailing slashes.
    return $path == '/' ? $path : rtrim($path, '/');
  }

  //Compare the regest pattern with a string.
  public function fullMatch($pattern, $string) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    //Match the pattern with the string and capture matched values.
    if(preg_match('#^' . $pattern . '$#', $string, $values)) {
      return $values;
    }

    return false;
  }

  public function rightMatch($pattern, $string) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    return substr($string, -1, strlen($pattern)) === $pattern;
  }

  //Transform the url into a regex pattern.
  //If a reference to the $matches array is supplied, capture the matches.
  public function toRegex($url, &$matches = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    if(!is_string($url)) {
      return '';
    }

    //If a matches is null create a new local array to capture the results.
    if(is_null($matches) || !is_array($matches)) {
      $matches = [];
    }

    $regex = preg_replace_callback('/(:\w+)/',
    function($captured) use (&$matches) {
      $matches[] = $captured[ 0 ];

      return '(\\w+)';
    }, $url);

    return $regex;
  }

  /*
  * Scan the supplied route directory and load the route definitions.
  */
  public function loadFiles($path = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    // Check if a path string is supplied and that its not empty.
    $directories = is_string($path) && !empty($path) ? $path : $this->getConfig('routes');

    // Check if an array of directories was supplied.
    // If not, convert it to an array.
    if(!is_array($directories)) {
      $directories = [$directories];
    }

    $count = count($directories);
    for($i = 0; $i < $count; ++$i) {

      $dir = $directories[$i];
      // Check the directory is a valid string.
      if(!is_string($dir) || !is_dir($dir)) { continue; }

      if($handle = opendir($dir)) {

        // Set the caching flag.
        $this->_caching = true;

        while(false !== ($entry = readdir($handle))) {

          // Validate the entry is not in the exluded path list.
          if($entry === '.' || $entry === '..') { continue; }

          //Build the full file path.
          $file = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $entry;

          // If entry is another directory, recursively call loadFiles.
          if(is_dir($file)) {
            $this->loadFiles($file);
            // Dont bother proceeding since this is a directory.
            continue;
          }

          // Test the entry against the matching regex.
          if(!preg_match($this->_routeFilePattern, $entry)) { continue; }

          if(is_file($file)) {
            $this->loadFile($file);
          } else {
            // Log Error.
          }
        }
        closedir($handle);
      }
    }
    return;
  }

  public function loadFile($filepath) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    /*
     * Check if the the file is already loaded.
     * this is to prevent infinite recursion if the 'route'
     * method is called from within a route file.
     * And accidentaly loading duplicate files.
     */
    if(isset($this->_loadedRouteFiles[$filepath])) {
      // Throw error?
      $error = [
        'class' => __CLASS__,
        'method' => __METHOD__,
        'error' => 'Attempting to load a duplicate route file: "' . $filepath . '"'
      ];
      $this->_error[] = $error;
      return false;
    }

    //Get the file info and check the extension.
    $info = pathinfo($filepath);

    if($info && isset($info[ 'extension' ]) && strtolower($info[ 'extension' ]) === 'php') {

      //Save the locations of the current route file.
      $this->_filename = $filepath;

      //Load the route file.
      $route = require($filepath);

      // Add the filepath to the loadedRouteFile registry.
      $this->_loadedRouteFiles[$filepath] = true;

      //Check that a function is provided in the '$route' variable.
      if(is_callable($route)) {
        // Call any 'before' Hooks.
        if($hook = $this->getHook('before', 'loadfile')) {
          $hook($route);
        } else {
          $route($this);
        }

        //reset the mount point that may have been set by the route file
        $this->_mountPoint = null;

        //The routefile is require by the loadRoute method to properly build a cache.
        $this->_filename = null;

        return true;
      }
    }

    return false;
  }

  public function getRoutingTable() {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    return $this->_routingTable;
  }
  /*
  * ---- Private methods ----
  */
  private function init() {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
  }

  private function updateRoutingTable($data) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    //Extract the method and pattern values.
    $method = isset($data['method']) ? $data['method'] : '';
    $pattern = isset($data['pattern']) ? $data['pattern'] : '';

    if(!isset($this->_routingTable[$method]) || !is_array($this->_routingTable[$method])) {
      $this->_routingTable[$method] = [];
    }
    // Count the number of nodes in the $pattern.
    $count = strval($this->nodeCount($pattern));

    if(!isset($this->_routingTable[ $method ][ $count ])) {
      $this->_routingTable[ $method ][ $count ] = [];
    }
    $this->_routingTable[$method][$count][$pattern] = $data;

    return $this;
  }

  private function findRoute($method, $url, $call = 0) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    $count = strval($this->nodeCount($url));
    $route = null;

    // Check if the route exists in the routingTable.
    if(isset($this->_routingTable[ $method ][ $count ]) &&
      is_array($this->_routingTable[ $method ][ $count ]) &&
      !empty($this->_routingTable[ $method ][ $count ])) {

      //Check if exact match exists.
      if(isset($this->_routingTable[ $method ][ $count ][ $url ])) {
        $route = $this->_routingTable[ $method ][ $count ][ $url ];
      } else {
        foreach($this->_routingTable[ $method ][ $count ] as $pattern => $data) {
          // Try to match the URL and extract the propery names
          if($pValues = $this->fullMatch($pattern, $url)) {
             // Unshift the $url from the first array element.
             $url = array_shift($pValues);
             // Extract the parameter names from the data object.
             $pNames = isset($data['parameters']) ? $data['parameters'] : null;
             $route = $data;
             $route['parameters'] = $this->buildParams($pNames, $pValues);
             break;
          }
        }
      }
    }
    return $route;
  }
  private function getRoute($method, $url) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    // Try to find the route in the current routing table.
    $route = $this->findRoute($method, $url);

    // If the route does not have a valid handler attempt to load the
    // file assocciated with the filepath.
    if(!isset($route['handler']) || !is_callable($route['handler'])
        && is_string($route['filename']) && !empty($route['filename'])) {
        $this->loadFile($route['filename']);

        //Get the route data.
        $route = $this->findRoute($method, $url);
    }

    return $route;
  }

  /*
   * @method _called - Used for debugging purposes. pushes the supplied
   *  function name and parameters to the callstack.
   */
  private function _called($func, $params = '') {
    $params = array_map( function($o) {
      if(!is_scalar($o)) {
        return '[object]';
      } else {
        return $o;
      }
    }, $params);
    $this->_callstack[] = $func . ': ' . implode(' | ',$params);
  }
  private function setRoute($method, $url, $handler) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }

    if(is_string($this->_mountPoint) && !empty($this->_mountPoint)) {
      $url = $url === '/' ? $this->_mountPoint : $this->_mountPoint . $url;
    }


    //matched parameters names
    $pNames = [];

    $pattern = $this->toRegex($url, $pNames);

    // Add the route data to the routingTable.
    $this->updateRoutingTable(['method' => $method,
                                'pattern' => $pattern,
                                'handler' => $handler,
                                'parameters' => $pNames,
                                'filename' => $this->_filename]);
    return $this;
  }

  //combine the parameter names and values into an associative array to be passed to the handler.
  private function buildParams($pNames, $pValues) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $params = [];
    $nCount = count($pNames);
    $vCount = count($pValues);

    if($nCount === $vCount && !is_null($pNames) &&
    !empty($pNames) && !empty($pValues)
    ) {
      for($i = 0; $i < $vCount; ++$i) {
        //Remove the initial ":" from the start of the string.
        $params[ ltrim($pNames[ $i ], ':') ] = $pValues[ $i ];
      }
    }
    return $params;
  }


  /*
   * @method executeMiddleware()
   * @param String $pattern - Regex pattern for the uri.
   * @param Array $params - Array of route parameters.
   * @return Array - Returns an Array items returned from each function call.
   */
  private function executeMiddleware($pattern, $params) {

    $args = [];

    $middleware = $this->_reduceMiddleware($pattern);
    // Execute the 'before' hooks.
    $preHook = $this->getHook('before', 'middleware');
    $postHook = $this->getHook('after', 'middleware');

    if($mCount = count($middleware)) {
      for($i = 0; $i < $mCount; ++$i) {
        $fn = $middleware[$i];
        // Execute the 'before' hooks.
        if(is_callable($preHook)) {

          /*
           * Wrapper anonymous function that will update the _calledMiddleware
           * array when the 'before' hook executes the handler.
           * the function is staticaly bound to the current $this object
           * and scope.
           */
          $wrapper = (function() use ($fn) {
            /*
             * Call the handler with the same args that were used to
             * call the wrapper.
             */
            $result = call_user_func_array($fn, func_get_args());

            // Save a reference to the called handler.
            $this->_calledMiddleware[] = $fn;

            return $result;

          })->bindTo($this, 'static');

          $args[] = $preHook($wrapper ,$params, $args);
        } else {
          $args[] = $fn($params, $args);
          // Save a reference to the executed middleware function
          $this->_calledMiddleware[] = $fn;
        }

        // Execute the 'after' hooks.
        if(is_callable($postHook)) {
          $args[] = $postHook($fn,$params, $args);
        }
      }
    }
    return $args;
  }

  private function handleRequest($route) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    $handler = $route['handler'];
    $params = isset($route['parameters']) ? $route['parameters'] : null;
    $pattern = isset($route['pattern']) ? $route['pattern'] : null;
    //Check if the request has already been handled.
    if($this->_resolved === true) { return $this; }

    $args = [];
    //Check if middleware is supplied.
    if(!empty($this->_middleware)) {
      $args = $this->executeMiddleware($pattern, $params);
    }

    //Check if the '$handler' is an array of callbacks.
    if(is_array($handler)) {
      //call the handlers in a sequence.
      $count = count($handler);
      for($i = 0; $i < $count; ++$i) {
        $args[] = $this->resolve($handler[ $i ], $params, $args);
      }
    } else {
      $this->resolve($handler, $params, $args);
    }

    /*
     * Set the resolved flag to true if all of the handlers
     * have been called.
     */
    $this->_resolved = true;

    /*
     * Request has been handled, terminate the script
     * unless exitonsucess flag was set to false.
     */
    if($this->getConfig('exitonsucess') === false) {
      return $this;
    }
    exit(0);
  }

  private function resolve($handler, $params = null, $args = null) {
    if($this->_debug) {
      $this->_called(__FUNCTION__, func_get_args());
    }
    if(is_callable($handler)) {
      //Results from the handler.
      $result = null;

      // Check if a pre resolve hook is set, and call that passing in the handler.
      if($hook = $this->getHook('before', 'resolve')){
        /*
         * Wrapper anonymous function that will update the _calledHandlers
         * array when the 'before' hook executes the handler.
         * the function is staticaly bound to the current $this object
         * and scope.
         */
        $wrapper = (function() use ($handler) {
          /*
           * Call the handler with the same args that were used to
           * call the wrapper.
           */
          $result = call_user_func_array($handler, func_get_args());

          // Save a reference to the called handler.
          $this->_calledHandlers[] = $handler;

          return $result;

        })->bindTo($this, 'static');

        $result = $hook($wrapper, $params, $args);
      } else {
        $result = $handler($this, $params, $args);

        // Save a reference to the executed handler function.
        $this->_calledHandlers[] = $handler;
      }

      //Check for an after resolve hook.
      if($hook = $this->getHook('after', 'resolve')){
        $result = $hook($result);
      }

      return $result;
    } else {
      throw new Exception('The supplied handler method is not callable');
    }
  }
}

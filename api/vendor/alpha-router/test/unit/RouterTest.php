<?php
require_once('../../router.class.php');

class RouterTest extends PHPUnit_Framework_TestCase {
  protected $Router = null;

  public static function setUpBeforeClass() {}

  protected function setUp() {
    $config = [
      'router' => [
        'exitonsucess' => false,
        'buildCache' => false
      ]
    ];

    //$this->Router = $this->App->router();
    $this->Router = new Router($config['router']);
  }

  public function test_should_exist() {
    $this->assertNotNull($this->Router, 'Router object should be created.');
  }

  //Setup tests for route matching.
  public function test_should_call_exact_match_handler() {
    $this->Router->mount('/account')
    ->get('/1/users/2', function($router, $params) {
      $this->assertNotNull($router, 'Route handler should get Router instance as the first parameter be default.');
      $this->assertInstanceOf('router', $router, 'First parameter should be an instance of Router by default');
      $this->assertEmpty($params, 'Route handler should not get $params parameter for a none parameterized url.');
    })
    ->get('/1/users/:id', function($router, $params) {
      $this->fail('Router handler should not call parameterized url if an exact match exists');
    })
    ->route('/account/1/users/2');
    $this->assertCount(1, $this->Router->getCalledHandlers(), ' Proper Number of handlers should be called');
    $this->assertTrue($this->Router->done(), 'Router->done() should return true if a handler was called.');
  }

  public function test_should_call_proper_handler_with_params() {
    $this->Router->mount('/account')
    ->get('/:accountId/users/:userId', function($router, $params) {
      $this->assertNotNull($router, 'Route handler should get Router instance as the first parameter be default.');
      $this->assertInstanceOf('router', $router, 'First paremember should be an instance of Router by default');
      $this->assertNotNull($params, 'Route handler should get $params as the second parameter.');
      $this->assertCount(2, $params, '$params should be equal to 2');
      //Assert proper $parametes.
      $this->assertArrayHasKey('accountId', $params, '$params should have a key "accountId"');
      $this->assertEquals($params['accountId'], '1', '$params should have a key "accountId" with value 1');
      $this->assertArrayHasKey('userId', $params, '$params should have a key "userId"');
      $this->assertEquals($params['userId'], '2', '$params should have a key "userId" with value 2');
    })->route('/account/1/users/2');

    $this->assertCount(1, $this->Router->getCalledHandlers(), ' Proper Number of handlers should be called');
    $this->assertTrue($this->Router->done(), 'Router->done() should return true if a handler was called.');
  }

  /**
   * @group single
   */
  public function test_fallback_handler() {
    $Router = $this->Router;

    // Route should not be called.
    $Router->get('/fail', function($params, $args) {
      $this->fail('Route should not be called');
    });

    $this->assertFalse($Router->route('/test'),
      'Should return false if no matching route is found.');

    $fallback = function($router, $params, $args) {};

    $Router->fallback($fallback);
    $result = $Router->route('/test');
    $this->assertEquals($result, $Router,
      'Should return a reference to self if a handler was called.');

    $this->assertCount(1, $Router->getCalledHandlers(),
      'Should return the called handlers.');
  }

  /**
   * @group
   */
  public function test_should_count_uri_nodes() {

    $this->assertEquals(0, $this->Router->nodeCount('/'));
    $this->assertEquals(1, $this->Router->nodeCount('/users'));
    $this->assertEquals(2, $this->Router->nodeCount('/users/add'));
    $this->assertEquals(2, $this->Router->nodeCount('/users/add/'));
  }

  /**
   * @group
   */
  public function test_should_store_and_retrieve_middleware() {
    $this->Router
      ->middleware(function($args) {})
      ->middleware('/', function($args) {})
      ->middleware('/account', function($args) {})
      ->middleware('/account/all', function($args) {})
      ->middleware('/account/:id/users', function($args) {})
      ->middleware('/account/:id/users/get/all', function($args) {})
      ->middleware('/users/all', function($args) {})
      ->middleware('/users/remove/all', function($args) {});

      // Get all middleware.
      $this->assertCount(8, $this->Router->getMiddleware(),
        'Correct number of Middleware should be added to the router');

      $this->assertCount(2, $this->Router->getMiddleware('/'),
        'Should retrieve all middleware attached to root');

      $this->assertCount(3, $this->Router->getMiddleware('/account'),
        'Should retrieve all middleware upto and including the node');

      $this->assertCount(4, $this->Router->getMiddleware('/account/all'));

      $this->assertCount(3, $this->Router->getMiddleware('/account/:id'));

      $this->assertCount(4, $this->Router->getMiddleware('/account/:id/users'));

      $this->assertCount(4, $this->Router->getMiddleware('/account/:id/users/get'));

      $this->assertCount(5, $this->Router->getMiddleware('/account/:id/users/get/all'));

      $this->assertCount(3, $this->Router->getMiddleware('/users/all'));

      $this->assertCount(3, $this->Router->getMiddleware('/users/remove/all'));

      $this->assertCount(2, $this->Router->getMiddleware('/users/remove'));
  }

  public function test_should_call_proper_middleware_for_partial_match() {
    $this->Router
      ->middleware('/account', function($args) {})
      ->middleware('/account/:id/users', function($args) {})
      ->middleware('/account/:id/users/get/all', function($args) {})
      ->middleware('/account/all', function($args) {});

    $this->assertFalse($this->Router->route('/account/1/users/2'),
      'Middleware Should not be called without defined route handlers');

    $this->Router->get('/account/:accountId/users/:userId', function($router, $params) {
      $this->assertNotNull($router, 'Route handler should get Router instance as the first parameter be default.');
      $this->assertCount(2, $params, '$params should be equal to 2');
    })->route('/account/1/users/2');

    $this->assertCount(4, $this->Router->getMiddleware(), 'Correct number of Middleware should be added to the router');
    $this->assertCount(2, $this->Router->getCalledMiddleware(), 'Correct number of Middleware handlers should be called');
    $this->assertCount(1, $this->Router->getCalledHandlers(), 'Correct number of Handlers should be called');
  }

  /**
   * @group
   */
  public function test_before_middleware_hooks() {

    $this->Router->before('middleware', function($fn, $args) {
      return $fn($args);
    });

    $this->Router
      ->middleware('/account', function($args) {})
      ->middleware('/account/:id/users', function($args) {})
      ->middleware('/account/:id/users/get/all', function($args) {})
      ->middleware('/account/all', function($args) {});

    $this->Router->get('/account/:accountId/users/:userId', function($router, $params) {
      $this->assertNotNull($router, 'Route handler should get Router instance as the first parameter be default.');
      $this->assertCount(2, $params, '$params should be equal to 2');
    })->route('/account/1/users/2');

    $this->assertCount(4, $this->Router->getMiddleware(), 'Correct number of Middleware should be added to the router');
    $this->assertCount(2, $this->Router->getCalledMiddleware(), 'Correct number of Middleware handlers should be called');
    $this->assertCount(1, $this->Router->getCalledHandlers(), 'Correct number of Handlers should be called');
  }

  // Pre and Post Hooks
  public function test_should_call_all_handlers_and_pass_args() {
    $resolved = false;
    // Should Execute the array of callable objects in order.
    $handlers = [
      function($router, $params, $args) {
        $this->assertNotNull($router, 'Router should be passed as the first parameter');
        $this->assertEquals(0, count($args), 'Args should be empty for the first handler if no matching middleware exists');
        return 'test';
      },
      function($router, $params, $args) use (&$resolved) {
        $resolved = true;
        $this->assertNotNull($router, 'Router should be passed as the first parameter');
        $this->assertNotNull($args, 'Should capture args from the previous handler.');
        $this->assertEquals(1, count($args));
        $this->assertContains('test', $args);
      }
    ];

    $this->Router->get('/account/:accId/user/:userId', $handlers);
    $this->Router->route('/account/2/user/1');

    $this->assertTrue($resolved, 'Last Route handler should resolve the request.');
    $this->assertCount(2, $this->Router->getCalledHandlers(), 'Correct number of route handlers should be called.');
    $this->assertEquals($handlers, $this->Router->getCalledHandlers(), 'Correct number of route handlers should be called.');
  }

  /**
   * @group
   */
  public function test_before_resolve_hooks_with_single_handler() {
    $Router = $this->Router;

    $handler = function($params){
      $this->assertEquals(['value' => '1'], $params, '$params should contain the URL parameters');
    };

    // Setup the route Handler.
    $Router->get('/test/:value', $handler);

    $Router->before('resolve', function($handler, $params, $args) {
      $this->assertEquals(['value' => '1'], $params, '$params should contain the URL parameters');
      $handler($params, $args);
    });

    $Router->route('/test/1');

    $calledHandlers = $Router->getCalledHandlers();
    $this->assertCount(1, $calledHandlers,
      'Before resolve hook should updated the _calledHandlers array');

    $this->assertEquals($handler, $calledHandlers[0],
      'Before resolve hook should call the same handler that was supplied');
  }

  /**
   * @group
   */
  public function test_before_resolve_hooks_with_multiple_handlers() {
    $Router = $this->Router;

    $handlers = [
      function($params){
        $this->assertEquals(['value' => '1'], $params, '$params should contain the URL parameters');
        return 1;
      },
      function($params, $args){
        $this->assertEquals(['value' => '1'], $params, '$params should contain the URL parameters');
        $this->assertEquals([1], $args, '$args should contain the return values from previous handlers');
      }
    ];

    // Setup the route Handler.
    $Router->get('/test/:value', $handlers);

    $Router->before('resolve', function($handler, $params, $args) {
      $this->assertEquals(['value' => '1'], $params, '$params should contain the URL parameters');

      // Return the results from the handler.
      return $handler($params, $args);
    });

    $Router->route('/test/1');

    $calledHandlers = $Router->getCalledHandlers();
    $this->assertCount(2, $calledHandlers,
      'Before resolve hook should updated the _calledHandlers array');

    $this->assertEquals($handlers, $calledHandlers,
      'Before resolve hook should call the same handlers that were supplied');
  }
  // Validate routes to prevent duplicates.
  //Setup tests for middleware execution.
  //Setup tests for default fallback routes.
}

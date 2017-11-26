<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/patients');

  $router->get('/list', function($router, $params) use ($req, $db) {

  });

  $router->get('/:id', function($router, $params) use ($req, $db) {

  });

  $router->get('/:id/conditions', function($router, $params) use ($req, $db) {

  });

  $router->post('/:id/conditions', function($router, $params) use ($req, $db) {

  });
};

?>

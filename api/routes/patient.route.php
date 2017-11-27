<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/patient');

  $router->post('/', function($router, $params) use ($req, $db) {

    $patient = $db->model('patient');
    $patient->set($req->raw());
    if($patient->save()) {
      echo(json_encode($patient->toArray()));
    } else {
      echo(json_encode($patient->getErrors()));
    }
  });

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

<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/patients');

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

    $sql = "SELECT *
            FROM person as p1, patient as p2
            WHERE p1.id = p2.id";

    $result = $db->query($sql);

    echo(json_encode($result));


  });

  $router->get('/:id', function($router, $params) use ($req, $db) {

  });

  $router->get('/:id/conditions', function($router, $params) use ($req, $db) {

  });

  $router->post('/:id/conditions', function($router, $params) use ($req, $db) {

  });
};

?>

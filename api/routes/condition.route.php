<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/conditions');

  $router->post('/', function($router, $params) use ($req, $db) {

    $condition = $db->model('condition');

    $condition->set($req->raw());
    if($condition->save()) {
      echo(json_encode($condition->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($condition->getErrors()));
    }

  });

  $router->get('/:id', function($router, $params) use ($req, $db) {
    $person = $db->model('condition')->load($params['id']);
    if(!$person->isEmpty()) {
      echo(json_encode($person->toArray()));
    } else {
      http_response_code(404);
    }

  });

  $router->get('/types', function($router, $params) use ($req, $db) {

    $types = $db->model('condition_type')->all();
    echo(json_encode($types->toArray()));

  });

  $router->get('/search', function($router, $params) use ($req, $db) {
    //print_r($router);

    echo(json_encode(['test' => 'search']));

    /*
    if($person->save()) {
      echo(json_encode($person->toArray()));
    }
    */
  });
};

?>

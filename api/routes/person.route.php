<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/person');

  $router->post('/', function($router, $params) use ($req, $db) {

    $person = $db->model('person');
    $person->set($req->raw());
    if($person->save()) {
      echo(json_encode($person->toArray()));
    } else {
      echo(json_encode($person->getErrors()));
    }

  });

  $router->get('/:id', function($router, $params) use ($req, $db) {
    $person = $db->model('person')->load($params['id']);
    if(!$person->isEmpty()) {
      echo(json_encode($person->toArray()));
    } else {
      header("Status: 404 Not Found");
    }

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

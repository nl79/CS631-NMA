<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/facilities');

  $router->post('/rooms', function($router, $params) use ($req, $db) {

    $patient = $db->model('room');
    $patient->set($req->raw());
    if($patient->save()) {
      echo(json_encode($patient->toArray()));
    } else {
      echo(json_encode($patient->getErrors()));
    }
  });

  $router->get('/rooms', function($router, $params) use ($req, $db) {

    $room = $db->model('room')->all();

    echo(json_encode($room->toArray()));

  });

  $router->get('/rooms/:id', function($router, $params) use ($req, $db) {

    $room = $db->model('room')->load($params['id']);

    if(!$room->isEmpty()) {
      echo(json_encode($room->toArray()));
    } else {
      header("Status: 404 Not Found");
    }

  });

  $router->post('/:id/condition', function($router, $params) use ($req, $db) {
    $pc = $db->model('patient_condition');
    $pc->set($req->raw());
    // Set the patient id from the route $params
    $pc->set('patient', $params['id']);
    if($pc->save()) {
      echo(json_encode($pc->toArray()));
    } else {
      echo(json_encode($pc->getErrors()));
    }

  });

  $router->get('/rooms/:id/beds', function($router, $params) use ($req, $db) {

    $bed = $db->model('bed')->where('rnum', $params['id']);

    if(!$bed->isEmpty()) {
      echo(json_encode($bed->toArray()));
    } else {
      http_response_code(404);
    }

  });

  $router->post('/rooms/:id/beds', function($router, $params) use ($req, $db) {

    $bed = $db->model('bed')->set($req->raw());
    
    if($bed->save()) {
      echo(json_encode($bed->toArray()));
    } else {
      echo(json_encode($bed->getErrors()));
    }

  });


};

?>

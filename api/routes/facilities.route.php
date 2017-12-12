<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/facilities');

  $router->post('/rooms', function($router, $params) use ($req, $db) {

    $patient = $db->model('room');
    $patient->set($req->raw());
    if($patient->save()) {
      echo(json_encode($patient->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($patient->getErrors()));
    }
  });

  $router->get('/rooms', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT r.*
            FROM room AS r ";

    if(!empty($q)) {
      $sql .= " WHERE r.type LIKE '%$q%'
              OR r.number LIKE '%$q%'";
    }

    $result = $db->query($sql);
    echo(json_encode($result));
  });

  $router->get('/rooms/:id', function($router, $params) use ($req, $db) {

    $room = $db->model('room')->load($params['id']);

    if(!$room->isEmpty()) {
      echo(json_encode($room->toArray()));
    } else {
      http_response_code(404);
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
      http_response_code(400);
      header('Content-Type: application/json');
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
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($bed->getErrors()));
    }

  });
};

?>

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

    $patient = $db->model('patient')->where('id', $params['id']);

    if(!$patient->isEmpty()) {
      echo(json_encode($patient->toArray()));
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

  $router->get('/:id/conditions', function($router, $params) use ($req, $db) {
    $sql = "SELECT c.id, c.`name`, c.description, ct.`name` as `type`
            FROM patient as p, patient_condition as pc, `condition` as c, condition_type as ct
            WHERE p.id = pc.patient
              AND pc.condition = c.id
              AND c.type = ct.id
              AND p.id = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/:id/staff', function($router, $params) use ($req, $db) {
    $sql = "";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/:id/staff', function($router, $params) use ($req, $db) {
    $sql = "";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/:id/profile', function($router, $params) use ($req, $db) {
    $sql = "SELECT *
            FROM person as p1, patient as p2
            WHERE p1.id = p2.id
            AND p2.id = " . $db->escape($params['id']);

    $result = $db->query($sql);
    if(empty($result)) {
      http_response_code(404);
      exit;
    } else {
      echo(json_encode($result[0]));
    }
  });
};

?>

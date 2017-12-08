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


    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/:id/staff', function($router, $params) use ($req, $db) {

    $ps = $db->model('patient_staff');
    $ps->set($req->raw());
    // Set the patient id from the route $params
    $ps->set('patient', $params['id']);
    if($ps->save()) {
      echo(json_encode($ps->toArray()));
    } else {
      echo(json_encode($ps->getErrors()));
    }

  });


  $router->delete('/:id/staff', function($router, $params) use ($req, $db) {

    $sql = 'DELETE
            FROM patient_staff
            WHERE patient = ' . $db->escape($params['id']) .
            ' AND staff = '  . $db->escape($req->get('staff'));

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/:id/staff', function($router, $params) use ($req, $db) {

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s, patient_staff as ps
            WHERE p.id = s.id
            AND s.id = ps.staff
            AND ps.patient = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/:id/staff/unassigned', function($router, $params) use ($req, $db) {

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT ps.staff
                FROM patient_staff as ps
                WHERE ps.patient = " . $db->escape($params['id']) . ")";

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

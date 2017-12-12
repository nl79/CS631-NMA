<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/scheduling');

  /* appointments END POINTS */

  $router->get('/appointments/search', function($router, $params) use ($req, $db) {
    $q = $db->escape($req->get('q'));

    $sql = "SELECT a.id, CONCAT(p.firstName,' ', p.lastName) AS Patient, a.type, a.description, a.date, a.time
            FROM appointment as a, person as p
            WHERE a.patient = p.id";

    if(!empty($q)) {
      $sql .= " AND (
                p.firstName LIKE '%$q%'
              OR p.lastName LIKE '%$q%'
              OR p.ssn LIKE '$q%'
              OR p.phnumb LIKE '$q%'
              OR a.`type` LIKE '$q%'
            )";
    }

    $sql .= " ORDER BY a.`date` DESC";

    //print_r($sql);
    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/appointments', function($router, $params) use ($req, $db) {

    $model = $db->model('appointment');
    $model->set($req->raw());
    if($model->save()) {
      echo(json_encode($model->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($model->getErrors()));
    }
  });

  $router->get('/appointments/:id', function($router, $params) use ($req, $db) {

    $model = $db->model('appointment')->load($params['id']);

    if(!$model->isEmpty()) {
      echo(json_encode($model->toArray()));
    } else {
      http_response_code(404);
    }
  });

  $router->get('/appointments', function($router, $params) use ($req, $db) {

    $sql = "SELECT a.id, CONCAT(p.firstName,' ', p.lastName) AS Patient, a.type, a.description, a.date, a.time
            FROM appointment as a, person as p
            WHERE a.patient = p.id
            ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));
  });

  $router->get('/appointments/:id/staff/unassigned', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT sa.staff
                FROM staff_appointment as sa
                WHERE sa.appt = " . $db->escape($params['id']) . ")";

    if(!empty($q)) {
      $sql .= " AND (p.firstName LIKE '%$q%'
              OR p.lastName LIKE '%$q%'
              OR p.ssn LIKE '$q%'
              OR p.phnumb LIKE '$q%')";
    }

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/appointments/:appt/staff/:staff', function($router, $params) use ($req, $db) {

    $model = $db->model('staff_appointment');
    // Set the patient id from the route $params
    $model->set('appt', $params['appt']);
    $model->set('staff', $params['staff']);
    if($model->save()) {
      echo(json_encode($model->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($model->getErrors()));
    }
  });

  $router->delete('/appointments/:appt/staff/:staff', function($router, $params) use ($req, $db) {

    $sql = 'DELETE
            FROM staff_appointment
            WHERE appt = ' . $db->escape($params['appt']) .
      ' AND staff = '  . $db->escape($params['staff']);

    $result = $db->query($sql);
    echo(json_encode($result));
  });



  $router->get('/appointments/:id/staff', function($router, $params) use ($req, $db) {

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s, staff_appointment as sa
            WHERE p.id = s.id
            AND s.id = sa.staff
            AND sa.appt = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });


  /* appointment facilities assignments */
  $router->get('/appointments/:id/rooms/unassigned', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT *
            FROM room as r
            WHERE r.id NOT IN
              ( SELECT ar.room
                FROM appointment_room as ar
                WHERE ar.appt = " . $db->escape($params['id']) . ")";

    if(!empty($q)) {
      $sql .= " AND (r.`type` LIKE '%$q%'
                OR r.number LIKE '%$q%'
              )";
    }

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/appointments/:appt/rooms/:room', function($router, $params) use ($req, $db) {

    $model = $db->model('appointment_room');
    // Set the patient id from the route $params
    $model->set('appt', $params['appt']);
    $model->set('room', $params['room']);
    if($model->save()) {
      echo(json_encode($model->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($model->getErrors()));
    }
  });

  $router->delete('/appointments/:appt/rooms/:room', function($router, $params) use ($req, $db) {

    $sql = 'DELETE
            FROM appointment_room
            WHERE appt = ' . $db->escape($params['appt']) .
      ' AND room = '  . $db->escape($params['room']);

    $result = $db->query($sql);
    echo(json_encode($result));
  });

  $router->get('/appointments/:id/rooms', function($router, $params) use ($req, $db) {

    $sql = "SELECT r.*
            FROM room as r, appointment_room as ar
            WHERE r.id = ar.room
            AND ar.appt = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });


  $router->get('/appointments/reports/staff/:id', function($router, $params) use ($req, $db) {
    $sql = "SELECT p.firstName, p.lastName, a.*
            FROM person as p, staff as s, appointment as a, staff_appointment as sa
            WHERE s.id = sa.staff
            AND a.id = sa.appt
            AND s.id = p.id
            AND sa.staff = " . $db->escape($params['id']) .
            " ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/appointments/reports/staff', function($router, $params) use ($req, $db) {
    $sql = "SELECT p.firstName, p.lastName, a.*
            FROM person as p, staff as s, appointment as a, staff_appointment as sa
            WHERE s.id = sa.staff
            AND a.id = sa.appt
            AND s.id = p.id
            ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/appointments/reports/patients/:id', function($router, $params) use ($req, $db) {
    $sql = "SELECT p.firstName, p.lastName, a.*
            FROM person as p, patient as p2, appointment as a
            WHERE a.patient = p2.id
            AND p2.id = p.id
            AND a.patient = " . $db->escape($params['id']) .
      " ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/appointments/reports/rooms/:id', function($router, $params) use ($req, $db) {
    $sql = "SELECT p.firstName, p.lastName, a.*
            FROM person as p, patient as p2, appointment as a, appointment_room as ar
            WHERE p2.id = p.id
            AND a.patient = p2.id
            AND ar.appt = a.id
            AND ar.room = " . $db->escape($params['id']) .
      " ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->get('/appointments/reports/rooms', function($router, $params) use ($req, $db) {
    $sql = "SELECT p.firstName, p.lastName, a.*
            FROM person as p, patient as p2, appointment as a, appointment_room as ar
            WHERE p2.id = p.id
            AND a.patient = p2.id
            AND ar.appt = a.id
            ORDER BY a.`date` DESC";

    $result = $db->query($sql);
    echo(json_encode($result));

  });



  /* SHIFT ENDPOINTS */

  $router->get('/shifts', function($router, $params) use ($req, $db) {

    $shift = $db->model('shift')->all();

    echo(json_encode($shift->toArray()));
  });

  $router->post('/shifts', function($router, $params) use ($req, $db) {

    $shift = $db->model('shift');
    $shift->set($req->raw());
    if($shift->save()) {
      echo(json_encode($shift->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($shift->getErrors()));
    }
  });

  $router->get('/shifts/:id', function($router, $params) use ($req, $db) {

    $shift = $db->model('shift')->load($params['id']);

    if(!$shift->isEmpty()) {
      echo(json_encode($shift->toArray()));
    } else {
      http_response_code(404);
    }
  });

  $router->get('/shifts/:id/staff', function($router, $params) use ($req, $db) {

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s, staff_shift as ss
            WHERE p.id = s.id
            AND s.id = ss.staff
            AND ss.shift = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/shifts/:shift/staff/:staff', function($router, $params) use ($req, $db) {

    $model = $db->model('staff_shift');
    // Set the patient id from the route $params
    $model->set('shift', $params['shift']);
    $model->set('staff', $params['staff']);
    if($model->save()) {
      echo(json_encode($model->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($model->getErrors()));
    }
  });

  $router->delete('/shifts/:shift/staff/:staff', function($router, $params) use ($req, $db) {

    $sql = 'DELETE
            FROM staff_shift
            WHERE shift = ' . $db->escape($params['shift']) .
      ' AND staff = '  . $db->escape($params['staff']);

    $result = $db->query($sql);
    echo(json_encode($result));
  });


  $router->get('/shifts/:id/staff/unassigned', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT ss.staff
                FROM staff_shift as ss
                WHERE ss.shift = " . $db->escape($params['id']) . ")";

    if(!empty($q)) {
      $sql .= " AND (p.firstName LIKE '%$q%'
                OR p.lastName LIKE '%$q%'
                OR s.`role` LIKE '%$q%'
              )";
    }

    $result = $db->query($sql);
    echo(json_encode($result));
  });
};

?>

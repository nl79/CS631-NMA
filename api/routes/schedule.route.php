<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/scheduling');

  /* appointments END POINTS */

  $router->post('/appointments', function($router, $params) use ($req, $db) {

    $model = $db->model('appointment');
    $model->set($req->raw());
    if($model->save()) {
      echo(json_encode($model->toArray()));
    } else {
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

    $model = $db->model('appointment')->all();
    echo(json_encode($model->toArray()));
  });

  $router->get('/appointments/:id/staff/unassigned', function($router, $params) use ($req, $db) {

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT sa.staff
                FROM staff_appointment as sa
                WHERE sa.appt = " . $db->escape($params['id']) . ")";

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
      echo(json_encode($model->getErrors()));
    }
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

    $sql = "SELECT *
            FROM room as r
            WHERE r.id NOT IN
              ( SELECT ar.room
                FROM appointment_room as ar
                WHERE ar.appt = " . $db->escape($params['id']) . ")";

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
      echo(json_encode($model->getErrors()));
    }
  });

  $router->get('/appointments/:id/rooms', function($router, $params) use ($req, $db) {

    $sql = "SELECT r.*
            FROM room as r, appointment_room as ar
            WHERE r.id = ar.room
            AND ar.appt = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });


  $router->get('/appointments/reports/:type/:id', function($router, $params) use ($req, $db) {



    print_r($params);

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

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT ss.staff
                FROM staff_shift as ss
                WHERE ss.shift = " . $db->escape($params['id']) . ")";


    $result = $db->query($sql);
    echo(json_encode($result));

  });
};

?>

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

    $sql = "SELECT s.id, p.`firstName`, p.`lastName`, s.`role`
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

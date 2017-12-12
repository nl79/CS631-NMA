<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/patients');

  $router->get('/search', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT *
            FROM person as p1, patient as p2
            WHERE p1.id = p2.id";

    if(!empty($q)) {
      $sql .= " AND (p1.firstName LIKE '%$q%'
              OR p1.lastName LIKE '%$q%'
              OR p1.ssn LIKE '$q%'
              OR p1.phnumb LIKE '$q%')";
    }


    $result = $db->query($sql);

    echo(json_encode($result));
  });

  $router->post('/', function($router, $params) use ($req, $db) {

    $patient = $db->model('patient');
    $patient->set($req->raw());
    if($patient->save()) {
      echo(json_encode($patient->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
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
      http_response_code(400);
      header('Content-Type: application/json');
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
      http_response_code(400);
      header('Content-Type: application/json');
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
  /*
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
  */

  $router->get('/:id/staff/unassigned', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT s.id, s.snum, p.`firstName`, p.`lastName`, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.id NOT IN
              ( SELECT ps.staff
                FROM patient_staff as ps
                WHERE ps.patient = " . $db->escape($params['id']) . ")";

    if(!empty($q)) {
      $sql .= " AND (p.firstName LIKE '%$q%'
              OR p.lastName LIKE '%$q%'
              OR p.ssn LIKE '$q%'
              OR p.phnumb LIKE '$q%')";
    }

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

  $router->get('/:id/beds/unassigned', function($router, $params) use ($req, $db) {

    $q = $db->escape($req->get('q'));

    $sql = "SELECT b.* , r.`type`
            FROM bed as b, room as r
            WHERE b.rnum = r.id
            AND b.id NOT IN
              ( SELECT pb.bed
                FROM patient_bed as pb
                WHERE pb.patient = " . $db->escape($params['id']) . ")";

    if(!empty($q)) {
      $sql .= " AND (r.`type` LIKE '%$q%'
              OR r.number LIKE '%$q%'
              OR b.number LIKE '%$q%'
              OR b.`size` LIKE '%$q%'
            )";
    }

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->post('/:patient/beds/:bed', function($router, $params) use ($req, $db) {

    $pb = $db->model('patient_bed');
    // Set the patient id from the route $params
    $pb->set('patient', $params['patient']);
    $pb->set('bed', $params['bed']);
    if($pb->save()) {
      echo(json_encode($pb->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($pb->getErrors()));
    }
  });

  $router->get('/:id/beds', function($router, $params) use ($req, $db) {

    $sql = "SELECT b.*, r.`type`
            FROM bed as b, room as r,  patient_bed as pb
            WHERE b.id = pb.bed
            AND b.rnum = r.id
            AND pb.patient = " . $db->escape($params['id']);

    $result = $db->query($sql);
    echo(json_encode($result));

  });

  $router->delete('/:patient/beds/:bed', function($router, $params) use ($req, $db) {

    $sql = 'DELETE
            FROM patient_bed
            WHERE patient = ' . $db->escape($params['patient']) .
          ' AND bed = '  . $db->escape($params['bed']);

    $result = $db->query($sql);
    echo(json_encode($result));
  });
};

?>

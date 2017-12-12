<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/staff');

  $router->post('/', function($router, $params) use ($req, $db) {

    $staff = $db->model('staff');
    $staff->set($req->raw());
    if($staff->save()) {
      echo(json_encode($staff->toArray()));
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo(json_encode($staff->getErrors()));
    }
  });


  $router->get('/search', function($router, $params) use ($req, $db) {
    $q = $db->escape($req->get('q'));

    $sql = "SELECT *
            FROM person as p, staff as s
            WHERE p.id = s.id";

    if(!empty($q)) {
      $sql .= " AND (p.firstName LIKE '%$q%'
              OR p.lastName LIKE '%$q%'
              OR p.ssn LIKE '$q%'
              OR p.phnumb LIKE '$q%')";
    }


    $result = $db->query($sql);

    echo(json_encode($result));
  });

  $router->get('/list', function($router, $params) use ($req, $db) {

    $sql = "SELECT *
            FROM person as p, staff as s
            WHERE p.id = s.id";

    $result = $db->query($sql);

    echo(json_encode($result));
  });

  $router->get('/inRole', function($router, $params) use ($req, $db) {

    $sql = "SELECT p.`firstName`, p.`lastName`, s.id, s.`role`
            FROM person as p, staff as s
            WHERE p.id = s.id
            AND s.`role` in ";

    $roles = $req->get('role');

    if(is_scalar($roles)) {
      $sql .= "(`$db->escape_string($roles)`)";
    } else if( is_array($roles)){

      // Escape Function
      $escape = function($n) use ($db) {
        return "'" . $db->escape_string($n) . "'";
      };

      // Set the field names.
      $sql .= '(' . implode(',', array_map($escape, $roles)) . ')';
    }

    $result = $db->query($sql);

    echo(json_encode($result));

  });

  $router->get('/:id', function($router, $params) use ($req, $db) {

    $staff = $db->model('staff')->where('id', $params['id']);

    if(!$staff->isEmpty()) {
      echo(json_encode($staff->toArray()));
    } else {
      header("Status: 404 Not Found");
    }

  });

  $router->post('/:id/skill', function($router, $params) use ($req, $db) {


  });

  $router->get('/:id/skills', function($router, $params) use ($req, $db) {

  });
};

?>

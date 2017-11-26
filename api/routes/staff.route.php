<?php

return function($router, $req = null, $db = null) {
  $router->mount('/api/staff');

  $router->get('/list', function($router, $params) use ($req, $db) {
    //print_r($router);

    echo(json_encode(['test' => 'staff']));

    /*
    if($person->save()) {
      echo(json_encode($person->toArray()));
    }
    */
  });
};

?>

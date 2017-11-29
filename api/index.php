<?php
  date_default_timezone_set('America/New_York');
  require_once('vendor/alpha-router/index.php');
  require_once('vendor/alpha-request/index.php');
  require_once('vendor/database/index.php');

  require_once('config.db.php');

  $router = new Router();
  $Request = new Request();
  $database = new database($dbargs);
  $database->setTimezone(date('P'));

  //Import routes.
  $personRoutes = require_once('routes/person.route.php');
  $patientRoutes = require_once('routes/patient.route.php');
  $staffRoutes = require_once('routes/staff.route.php');
  $conditionRoutes = require_once('routes/condition.route.php');

  // Register routes.
  $personRoutes($router, $Request, $database);
  $patientRoutes($router, $Request, $database);
  $staffRoutes($router, $Request, $database);
  $conditionRoutes($router, $Request, $database);

  // Execute the route.
  $router->route($Request->method(), $Request->getReqUri());
?>

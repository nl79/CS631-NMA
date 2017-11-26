<?php

$nodes = explode('/', $_SERVER["REQUEST_URI"]);

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $nodes[count($nodes)-1])) {
    return false;    // serve the requested resource as-is.
} else if(count($nodes) > 1 && strtolower($nodes[1]) === 'api') {
    include('./api/index.php');
} else {
    include("./client/index.html");
    //print_r($nodes);
    //print_r($_SERVER);
}
?>

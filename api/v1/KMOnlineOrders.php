<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Methods: GET");

require_once "./core/DBConnection.php";

$DBConnection = new DBConnection();
$con = $DBConnection->getConnection();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

  //Add the review into the table
  $sql = "SELECT * FROM `order_details` WHERE orderType='online' AND orderStatus !='9'";
  $result = $con->query($sql);

  if ($result !== NULL) {

    //Convert result set to JSON
    $rows = array();
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }

    header("HTTP/1.1 200 OK");
    http_response_code(200);
    echo stripslashes(json_encode($rows));
    return;
    
  } else {
    header("HTTP/1.1 400 Bad Request");
    http_response_code(400);
    $message = '{"message": "Could not fetch orders"}';
    echo stripslashes(json_encode($message));
    return;
  }

}else{
    header("HTTP/1.1 405 Method Not Allowed");
    http_response_code(405);
    $message = '{"message": "Method Not Allowed"}';
    echo stripslashes(json_encode($message));
    return;
}

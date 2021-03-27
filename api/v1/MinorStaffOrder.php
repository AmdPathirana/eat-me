<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Methods: GET");

require_once "./core/DBConnection.php";

$DBConnection = new DBConnection();
$con = $DBConnection->getConnection();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    //get staff id
    $staff_id = $_GET['staff_id'];

    //Get order details of assigned order
    $sql = "SELECT staff_order.orderId, customer.firstName, customer.lastName, order_details.amount,order_details.orderStatus FROM `staff_order` JOIN order_details ON staff_order.orderId=order_details.orderId JOIN customer ON order_details.customerId=customer.customerId WHERE staff_order.staffId='$staff_id'";

    $result = $con->query($sql);

    if ($result !== NULL) {

        $row = $result->fetch_assoc();

        header("HTTP/1.1 200 OK");
        http_response_code(200);
        echo stripslashes(json_encode($row));
        return;
    } else {
        header("HTTP/1.1 400 Bad Request");
        http_response_code(400);
        $message = '{"message": "Could not fetch orders"}';
        echo stripslashes(json_encode($message));
        return;
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    http_response_code(405);
    $message = '{"message": "Method Not Allowed"}';
    echo stripslashes(json_encode($message));
    return;
}

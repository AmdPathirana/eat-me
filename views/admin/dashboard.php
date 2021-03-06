<?php
session_start();
ob_start();
$staffid = $_SESSION['staffId'];
$name_first = $_SESSION['firstName'];
$name_last = $_SESSION['lastName'];
$roleId = $_SESSION['roleId'];
// date_default_timezone_set("Asia/Colombo");
// $timestamp = date($dateAndTime);
// $timestamp=time();
// echo $timestamp;

require_once './controllers/admin/DashBoardController.php';
$DashBoardController = new DashBoardController();

$result = null;
if (isset($_POST['logout'])) {
  $DashBoardController->logoutstaffMem();
}

//Import Koolreport
require_once "./koolreport/core/autoload.php";
require_once "./config/koolreportconfig.php";

use \koolreport\datasources\PdoDataSource;
use \koolreport\widgets\google\ColumnChart;
use \koolreport\widgets\google\BarChart;
use \koolreport\widgets\google\PieChart;
use \koolreport\widgets\google\LineChart;



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Global Styles -->
  <link rel="stylesheet" href="../../css/style.css" />
  <!-- Local Styles -->
  <link rel="stylesheet" href="../../css/adminStyles.css">
  <link rel="stylesheet" href="../../css/adminMenuUpdate.css">
  <title>Admin Dashboard</title>
</head>

<body>

  <!-- -----navi bar ---------- -->
  <form action="" method="POST">
    <div class="navbar">
      <div class="columns group">
        <div class="column is-2">
          <img src="../../img/logo.png" height=56 width="224" />
        </div>
        <div class="column is-6 ml-5"></div>
        <div class="column is-3 has-text-right nav-logout">
          <i class="fa fa-user" aria-hidden="true"></i>
          <span class="mr-1"><?php echo $staffid ?></span>
          <span class="mr-1"><?php echo $name_first, " ", $name_last ?></span>
          <button class="button is-primary" name="logout">Logout</button>
        </div>
      </div>
    </div>
  </form>
  <!--------xx-----navi bar --------xx------->

  <!----------- navigatable buttons------------>
  <?php
  if ($roleId == "1") {
  ?>
    <section>
      <div class="row buttons-row">
        <a href="/admin">
          <button class="button is-primary button-is-active   right-radius idle">Dash Board</button>
        </a>
        <a href="/inventory">
          <button class="button is-primary right-radius left-radius idle">Inventory</button>

        </a>
        <a href="/grn">
          <button class="button is-primary left-radius right-radius idle">GRN</button>
        </a>
        <a href="/admin/menu/update">
          <button class="button is-primary left-radius right-radius idle">Menu</button>
        </a>
        <a href="/admin/staffmanage">
          <button class="button is-primary left-radius idle">Staff Manage</button>
        </a>
      </div>
    </section>
  <?php
  } else {
    $DashBoardController->logoutstaffMem();
  }
  ?>
  <!-----XX------ navigatable buttons-----XX------->

  <section>
    <div class="columns group">
      <div class="column is-4">
        <div class="card">
          <h4 class="title">
            Ongoing <span class="orange-color">Orders</span>
          </h4>
          <h2 class="subtitle">🕑<?php $result = $DashBoardController->getOngoingOrders();
                                  $row = mysqli_fetch_assoc($result);
                                  echo $row['COUNT(*)'];
                                  ?></h2>
        </div>
      </div>
      <div class="column is-4">
        <div class="card">
          <h4 class="title">
            Completed <span class="orange-color">Orders</span>
          </h4>
          <h2 class="subtitle">🍔<?php $result = $DashBoardController->getCompletedOrders();
                                  $row = mysqli_fetch_assoc($result);
                                  echo $row['COUNT(*)'];
                                  ?></h2>
        </div>
      </div>
      <div class="column is-4">
        <div class="card">
          <h4 class="title">
            Today <span class="orange-color">Sales</span>
          </h4>
          <h2 class="subtitle is-success"><?php $result = $DashBoardController->getTodaySales();
                                          $count = 0;
                                          while ($row = mysqli_fetch_assoc($result)) {
                                            $count = $count + $row['amount'];
                                          }
                                          echo number_format((float)$count, 2, '.', '');

                                          ?>


          </h2>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-1 pl-1 pr-1">
    <h1 class="title has-text-centered ">Ongoing <span class="orange-color">Orders</span></h1>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer ID</th>
          <th>Amount</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>State</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $DashBoardController->getInventoryDetails();
        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
              <td><?php echo $row['orderId']; ?></td>
              <td><?php echo $row['customerId']; ?></td>
              <td><?php echo $row['amount']; ?></td>
              <td><?php echo $row['firstName']; ?></td>
              <td><?php echo $row['lastName']; ?></td>
              <td><?php echo $row['state']; ?></td>
              <td><?php echo $row['date']; ?></td>
            </tr>
        <?php
          }
        }
        ?>
      </tbody>
    </table>
  </section>

  <section class="mt-2 pl-1 pr-1">
    <h1 class="title has-text-centered ">Store <span class="orange-color">Status</span></h1>

    <div class="columns group">
      <div class="column is-4">
        <h5 class="title has-text-centered ">Ongoing Orders</h1>
          <?php
          PieChart::create(array(
            "dataSource" => (new PdoDataSource($connection))->query("
              SELECT orderType ,COUNT(orderType) as Count FROM order_details GROUP BY orderType
              "),
            "options" => array(
              "is3D" => true
            )
          ));
          ?>
      </div>
      <div class="column is-4">
        <h5 class="title has-text-centered ">Staff Distribution</h1>
          <?php
          ColumnChart::create(array(
            "dataSource" => (new PdoDataSource($connection))->query("
                SELECT roleId, COUNT(roleId) AS count FROM staff GROUP BY roleId")
          ));
          ?>
      </div>
      <div class="column is-4">
        <h5 class="title has-text-centered ">Payment Types</h1>
        <?php
          ColumnChart::create(array(
            "dataSource"=>(new PdoDataSource($connection))->query("
            SELECT paymentType, COUNT(paymentType) AS count FROM order_details GROUP BY paymentType"),
          ));
        ?>
      </div>
    </div>
  </section>

</body>

</html>
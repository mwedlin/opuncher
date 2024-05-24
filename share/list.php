<?php
  // Make a table with user punches.

  // Create an array with the controlls.
  $sql = "SELECT id, name FROM transmitters WHERE game = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "i", $_SESSION["game"]);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $trans = array();
  while ($next = mysqli_fetch_assoc($res)) {
    $next["punched"] = false;
    $trans[$next["id"]] = $next;
  }
  mysqli_stmt_close($stmt);
    
  // Add punches to the table
  $sql = "SELECT transmitter, time FROM punch WHERE user = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  while ($next = mysqli_fetch_assoc($res)) {
    $trans[$next["transmitter"]]["punched"] = true;
    $trans[$next["transmitter"]]["time"] = $next["time"];
  }
  mysqli_stmt_close($stmt);
    
  echo "<h2>Start: ".$_SESSION["start"]."</h2>\n";

  // Print table

  echo "<table class=\"table\">\n<tr><th scope=\"col\">!control!</th><th scope=\"com\">!punched!</th></tr>\n";
    
  foreach ($trans as $t) {
    if ($t["punched"]) {
      // Find time difference
      $datetime1 = new DateTime($_SESSION["start"]);
      $datetime2 = new DateTime($t["time"]);
      $interval = $datetime1->diff($datetime2);

      echo "<tr class=\"table-success\">";
      echo "<th scope=\"row\">".$t["name"]."</th>";
      echo "<td>".$interval->format('%H:%I:%s')."</td>";
      echo "</tr>\n";
    } else {
      echo "<tr class=\"table-light\"><th scope=\"row\">";
      echo $t["name"];
      echo "</th><td>!ltf!</td></tr>\n";
    }
  }
  echo "</table>\n";

  if ($_SESSION["finished"]) {
    echo "<h2>Mål: ".$_SESSION["finish"]."</h2>\n";
    $datetime1 = new DateTime($_SESSION["start"]);
    $datetime2 = new DateTime($_SESSION["finish"]);
    $interval = $datetime1->diff($datetime2);
    echo "<h2>!tottid! ".$interval->format('%H:%i')."</h2>\n";
  }
?>

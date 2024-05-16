<?php
  // Make sure we have logged in to the database.

  // require_once "/usr/local/share/ardf/session.php";
  require_once "/usr/local/share/ardf/config.php";
  $game = 1;
  $eventName = "<event name>";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Rävjakt <?php echo $eventName ?></title>
	<meta http-equiv="refresh" content="20">
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Custom Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body class="d-flex flex-column h-100">
      <main class="flex-shrink-0">
        <?php
    	  $sql = "SELECT id, full_name, running, start, finished, finish FROM users WHERE game = ?";
    	  $stmt = mysqli_prepare($link, $sql);
    	  mysqli_stmt_bind_param($stmt, "i", $game);
    	  mysqli_stmt_execute($stmt);
    	  $res = mysqli_stmt_get_result($stmt);
    	  $users = array();
    	  while ($next = mysqli_fetch_assoc($res)) {
      	    if ($next["running"] or $next["finished"]) {
	      $users[$next["id"]] = $next;
      	    }
    	  }
    	  mysqli_stmt_close($stmt);
    	  sort($users);
    
          $sql = "SELECT id, name FROM transmitters WHERE game = ?";
    	  $stmt = mysqli_prepare($link, $sql);
    	  mysqli_stmt_bind_param($stmt, "i", $game);
    	  mysqli_stmt_execute($stmt);
    	  $res = mysqli_stmt_get_result($stmt);
    	  $trans = array();
    	  while ($next = mysqli_fetch_assoc($res)) {
	    $trans[$next["id"]] = $next;
    	  }
    	  mysqli_stmt_close($stmt);
    	  sort($trans);

	  $sql = "SELECT user, transmitter, time FROM punch";
    	  $stmt = mysqli_prepare($link, $sql);
	  mysqli_stmt_execute($stmt);
    	  $res = mysqli_stmt_get_result($stmt);

    	  $punch = array();
    	  while ($next = mysqli_fetch_assoc($res)) {
      	    array_push($punch, $next);
    	  }
    	  mysqli_stmt_close($stmt);

          // Print table
          echo "<table class=\"table\">\n<tr><th scope=\"col\">Deltagare</th><th scope=\"col\">Mål</th>\n";
	  foreach ($trans as $t) {
            echo "<th scope=\"col\">".$t["name"]."</th>";
          }
          echo "</tr>\n";
          foreach ($users as $u) {
            if ($u["finished"]) {
              echo "<tr class=\"table-success\"><th scope=\"row\">".$u["full_name"]."</th><td>";
              $datetime1 = new DateTime($u["start"]);
              $datetime2 = new DateTime($u["finish"]);
              $interval = $datetime1->diff($datetime2);
              echo $interval->format('%H:%I:%S');
            } else {
              echo "<tr class=\"table-light\"><th scope=\"row\">".$u["full_name"]."</th><td>ej klar";
            }
            echo "</td>";
            foreach ($trans as $t) {
	      $found = False;
	      foreach ($punch as $p) {
	      	if ($p["user"] == $u["id"] and $p["transmitter"] == $t["id"]) {
                  $datetime1 = new DateTime($u["start"]);
            	  $datetime2 = new DateTime($p["time"]);
            	  $interval = $datetime1->diff($datetime2);
	    	  echo "<td>".$interval->format('%H:%I:%S')."</td>";
	    	  $found = True;
	        }
	      }
	      if (!$found) {
	        echo "<td align=\"left\">-</td>";
	      }
      	    }
	    echo "</tr>\n";
    	  }  
    	  echo "</table>\n";
    	?>
      </main>
        <!-- Footer-->
        <footer class="bg-white py-4 mt-auto">
            <div class="container px-5">
                <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                    <div class="col-auto"><div class="small m-0">Copyright &copy; Mikael Wedlin 2024</div></div>
                    <div class="col-auto">
                        <a class="small" href="#!">Privacy</a>
                        <span class="mx-1">&middot;</span>
                        <a class="small" href="#!">Terms</a>
                        <span class="mx-1">&middot;</span>
                        <a class="small" href="#!">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>

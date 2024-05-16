<?php
  // Make sure we have a valid session right from the beginning.

  require_once "/usr/local/share/ardf/session.php";

  // Validate that we have logged in
  if (! $_SESSION["valid_user"]) {
    $_SESSION["status"] = "Please log in";
    header('Location: /');
    exit;
  }
  
  // Check for punch code.
  if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    // Check if code is empty
    if(! empty(trim($_POST["code"]))){
      $punch_in = trim($_POST["code"]);
    };
    // print( "<p>Punch: ");
    // print($punch_in);
    // print("<br>\n");
  }
  if($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if ticket is empty
    if(! empty(trim($_GET["code"]))){
      $punch_in = trim($_GET["code"]);
    }
  }
?>

<?php

  //
  // Check if running
  //

  // Check that we are running
  if (! $_SESSION["running"]) {
    $_SESSION["status"] = "Punch failed, not logged in.";
    header('Location: /');
    exit;
  }
  
  // Validate punch code
  if (empty($punch_in)) {
    echo "Give parameter for punch.";
    exit;
  } else {
    // Prepare a select statement
    $sql = "SELECT game, id, name FROM transmitters WHERE code = ?";

    if($stmt = mysqli_prepare($link, $sql)){

      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_punch);

      // Set parameters
      // The filter is just an extra protection, SQL injection is
      // handled in mysqli_bind_param().
      $param_punch = filter_var(trim($punch_in), FILTER_SANITIZE_STRING);

      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)){
        /* store result */
        mysqli_stmt_store_result($stmt);
                
        // print("Rows: ");
        // print(mysqli_stmt_num_rows($stmt));
        // print("<br>\n");
      
        if(mysqli_stmt_num_rows($stmt) == 1){
          //
          // We have found a valid transmitter.
          //

          // Bind result variables
          mysqli_stmt_bind_result($stmt, $game, $id, $name);

          if(mysqli_stmt_fetch($stmt)){
            // Is it the active game?
            if ($game == $_SESSION["game"]) {
              // Yes it is, log the punch
	      $punch_valid = True;
            } else {
	      $punch_valid = False;
              $_SESSION["status"] = "Punch not logged, wrong game.!";
            }
          }
        } else {
	  $punch_valid = False;
          $_SESSION["status"] =  "Punch not logged, wrong code!";
        }
      } else {
	$punch_valid = False;        
        exit("Oops! Something went wrong. Please try again later.");
      }
      mysqli_stmt_close($stmt);
      if ($punch_valid) {
        // Check if already checked in
        $sql = "SELECT time FROM punch WHERE user = ? AND transmitter = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $_SESSION["id"], $id );
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);
	if(mysqli_stmt_num_rows($stmt) < 1){ // Not already checked in
	  mysqli_stmt_close($stmt);
          $sql = "INSERT INTO punch ( user, transmitter ) VALUES ( ?, ? )";
          $stmt = mysqli_prepare($link, $sql);
          mysqli_stmt_bind_param($stmt, "ii", $_SESSION["id"], $id );
          if (mysqli_stmt_execute($stmt)) {
            $_SESSION["status"] = "Punch OK!";
          } else {
            $_SESSION["status"] = "Stämpling misslyckades!";
          }
	} else { // Already checked in.
           $_SESSION["status"] = "Already punched that control!";
        }
        mysqli_stmt_close($stmt);
       }
    } else {
      exit("Oops! Something went wrong. Please try again later.");
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta http-equiv="Refresh" content="0; url=/" />
        <title>Closing</title>
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
    <body>
    <h1>Session closed</h1>
    </body>
</html>

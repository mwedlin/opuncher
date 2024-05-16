<?php
  // Require this file to check the session parameters.
  // Variables:
  //
  //  $_SESSION["valid_user"] true/false True if user is valid.
  //  $_SESSION["id"] User ID.
  //  $_SESSION["created_at"] Time when user was created.
  //  $_SESSION["game"] ID of the event the user is issued for.
  //  $_SESSION["name"] Short name of the user.
  //  $_SESSION["full_name"] Full name of the user.
  //  $_SESSION["running"] Boolean that is true if the user has started.
  //  $_SESSION["start"] Timestamp from when the user started.
  //  $_SESSION["finished"] True if user has finished.
  //  $_SESSION["finish"] Timestamp from when the user finished.
  //  $_SESSION["user_ident"] Issued user identification.
  //  $_SESSION["status"] Latest status message.

  $incbase = "/usr/local/share/ardf";
  
  require_once $incbase . "/config.php";

  //
  // Increase lifetime
  //

  // server should keep session data for AT LEAST 10 hours
  ini_set('session.gc_maxlifetime', 36000);

  // each client should remember their session id for EXACTLY 10 hours
  session_set_cookie_params(36000);
  
  session_start();

  //
  // Look for a new user ident and/or checkin in the HTML parameters.
  //

  // print( "<p>Method: ");
  // print($_SERVER["REQUEST_METHOD"]);
  // print("<br>\n");

  // Have we sent in a new user code?

  if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    // Check if user_ident is empty
    if(array_key_exists("ident", $_POST) && ! empty(trim($_POST["ident"]))){
      $user_ident_in = trim($_POST["ident"]);
    };
    // print( "<p>user_ident: ");
    // print($user_ident_in);
    // print("<br>\n");
    // print( "<p>checkin: ");
    // print(checkin_in);
    // print("<br>\n");
  }
  if($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if user_ident is empty
    if(array_key_exists("ident", $_GET) && ! empty(trim($_GET["ident"]))){
      $user_ident_in = trim($_GET["ident"]);
    }
  }

  if (empty($user_ident_in) && !empty($_SESSION["user_ident"])) {
    // No inparams given but we have an old one.
    $user_ident_in = $_SESSION["user_ident"];
  }

  // Validate new user_ident
  if (! empty($user_ident_in)) {

    // Check if we are a presenter
    // if ($user_ident_in == "presenter2021") {
    //   header("Location: https://vdo.crate.foi.se/?room=ITforsvarsdagen&hash=61aa&sl&l");
    //  exit("");
    //}

    // Prepare a select statement
    $sql = "SELECT id, game, created_at, name, full_name, running, start, finished, finish FROM users WHERE ident = ?";

    if($stmt = mysqli_prepare($link, $sql)){

      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_user_ident);

      // Set parameters
      // The filter is just an extra protection, SQL injection is
      // handled in mysqli_bind_param().
      $param_user_ident = filter_var(trim($user_ident_in), FILTER_SANITIZE_STRING);

      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)){
        /* store result */
        mysqli_stmt_store_result($stmt);
                
	// print("Rows: ");
      	// print(mysqli_stmt_num_rows($stmt));
        // print("<br>\n");
      
        if(mysqli_stmt_num_rows($stmt) == 1){
          //
          // We have found a valid user_ident.
          //

          // Bind result variables
          mysqli_stmt_bind_result($stmt, $id, $game, $created_at, $name, $full_name, $running, $start, $finished, $finish);

          if(mysqli_stmt_fetch($stmt)){
            // Store data in session variables
            $_SESSION["valid_user"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["game"] = $game;
            $_SESSION["created_at"] = $created_at;
            $_SESSION["name"] = $name;
            $_SESSION["full_name"] = $full_name;
            $_SESSION["running"] = $running;
	    $_SESSION["start"] = $start;
	    $_SESSION["finished"] = $finished;
	    $_SESSION["finish"] = $finish;
	    $_SESSION["user_ident"] = $param_user_ident;
          }
        } else {
	  // We have got a non-existent user_ident.
          $_SESSION["valid_user"] = false;
	}

      } else {
        exit("Oops! Something went wrong. Please try again later.");
      }
      mysqli_stmt_close($stmt);
    } else {
      exit("Oops! Something went wrong. Please try again later.");
    }

  } else {
    if ( empty($_SESSION["valid_user"])) {
      $_SESSION["valid_user"] = false;
    }
  }

  if (empty($_SESSION["status"])) {
    $_SESSION["status"] = "";
  }
?>

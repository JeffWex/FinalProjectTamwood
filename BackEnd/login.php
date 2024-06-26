<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
require("./config.php");

//Connect to DB
function dbConnect()
{
  global $dbServer, $dbUser, $dbPass, $dbName;
  $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
  if ($dbCon->connect_error) {
    die("Connection to database failed. " . $dbCon->connect_error);
  }
  return $dbCon;
}

//Confirm email address
function getEmail($dbCon, $email)
{
  $selectCmd = $dbCon->prepare("SELECT * FROM user_tb WHERE email = ?");
  $selectCmd->bind_param("s", $email);
  $selectCmd->execute();
  $emailResult = $selectCmd->get_result();
  return $emailResult->num_rows > 0 ? $emailResult->fetch_assoc() : null;
}

//Check by id if this user is in blacklisted 
function checkBlacklist($userId, $dbCon)
{
  $selectId = "SELECT * FROM blacklist_tb WHERE id=" . $userId;
  $idResult = $dbCon->query($selectId);
  return $idResult->num_rows > 0;
}

//Set ecount back to 5.
function resetEcount($dbCon, $userId)
{
  $udUser = $dbCon->prepare("UPDATE user_tb SET ecount = 5 WHERE id = ?");
  $udUser->bind_param("i", $userId);
  $udUser->execute();
}

//Reduce the ecount by 1
function updateEcount($dbCon, $userId)
{
  $udUser = $dbCon->prepare("UPDATE user_tb SET ecount = ecount - 1 WHERE id = ?");
  $udUser->bind_param("i", $userId);
  $udUser->execute();
}

//Insert into block list
function blockUser($userId, $dbCon)
{
  $insBlock = $dbCon->prepare("INSERT INTO blacklist_tb(id) VALUES (?)");
  $insBlock->bind_param("i", $userId);
  $insBlock->execute();
}

function loginUser($email, $password)
{
  global $dbCon;

  if (!$email) {
    echo "User not found.";
    return null;
  } else {
    $user = getEmail($dbCon, $email);
    if ($user) {
      if (!checkBlacklist($user["id"], $dbCon)) {
        if (password_verify($password, $user["password"])) {
          if ($user["ecount"] > 0) {
            resetEcount($dbCon, $user["id"]);

            // session_start();
            $_SESSION["loginUser"] = $user;
            $_SESSION["timeout"] = time() + 300;

            // echo "Login success for user type: ";
            redirectBasedOnUserType($user);
          }
        } else {
          updateEcount($dbCon, $user["id"]);
          echo "Incorrect username/password.";

          if ($user["ecount"] <= 0) {
            blockUser($user["id"], $dbCon);
            echo " Your account is blocked. Please ask the support team to unblock.";
          }
        }
      } else {
        echo "Your account is blocked. Please ask the support team to unblock.";
      }
      return $user;
    } else {
      echo "User not found.";
      return null;
    }
  }
}

function redirectBasedOnUserType($user)
{
  if (session_status() === PHP_SESSION_ACTIVE) {
    switch ($user["user_type"]) {
      case "C":
        echo "Customer ".json_encode($user);
        break;
      case "S":
        if($user["new_staff"] == true){
          echo "Staff".json_encode($user);
        }else{
          echo "It's new Staff need be approval by Admin.";
        }
        break;
      case "A":
        echo "Admin".json_encode($user);
        break;
      default:
        echo "Invalid user type.";
        exit();
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["mode"])) {
    $mode = $_POST["mode"];
    session_start();
    if (isset($_POST["sid"])) {
      session_id($_POST["sid"]);
      if (isset($_SESSION["timeout"]) && $_SESSION["timeout"] > time()) {
        $_SESSION["timeout"] = time() + 10; // 5 minutes
      } else {
        session_unset();
        session_destroy();
      }
    }

    switch ($_POST["mode"]) {
      case "login":
        $dbCon = dbConnect();
        $user = loginUser($_POST["email"], $_POST["password"]);

        $dbCon->close();
        break;

      // Add other cases as needed

      default:
        // Handle other modes
        break;
    

        case "blk":  // Only admin user can see blacklist details
          session_start();
          if (isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["user_type"] == "A") {
              $dbCon = dbConnect();
              
              $selectCmd = "SELECT user_tb.id, fname, lname, mobile, email, user_type, time FROM user_tb INNER JOIN blacklist_tb ON user_tb.id=blacklist_tb.id";
              $result = $dbCon->query($selectCmd);
              
              if ($result->num_rows > 0) {
                  $blkUser = [];
                  while ($user = $result->fetch_assoc()) {
                      array_push($blkUser, $user);
                  }
                  echo json_encode($blkUser);
              } else {
                  echo "No Record";
              }
              
              $dbCon->close();
          } else {
              echo "User not authorized"; // show this message when logged-in user is not an admin
          }
          break;
        }      
  }
}

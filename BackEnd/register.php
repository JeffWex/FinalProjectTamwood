<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
include("./connect.php");    //solved

$regTb = "user_tb";


$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection error.']));
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        AddReg($conn);
        break;
    default:
        die(json_encode(['error' => 'Invalid request method.']));
}

function AddReg($conn) {
    $data = json_decode(file_get_contents('php://input'), true);

    $selectCmd = "SELECT email FROM user_tb WHERE email='".$_POST["email"]."'";
                $result = $conn->query($selectCmd);
                if($result->num_rows > 0){
                    echo "Registration failed!";
                    $conn->close();
                }
                else{
                    $new_staff = false;      // solved
                    $insCmd = 'INSERT INTO user_tb (fname,lname,mobile,email,password,user_type,new_staff) VALUES (?,?,?,?,?,?,?)';
                    $stmt = $conn->prepare($insCmd);
                    $stmt->bind_param("ssssssi",$data["fname"],$data["lname"],$data["mobile"],$data["email"],password_hash($data["password"],PASSWORD_BCRYPT,["cost"=>10]),$data["user_type"],$new_staff);
                
                    if ($stmt->execute()) {
                        echo json_encode(['message' => 'Menu added successfully.']);
                    } else {
                        echo json_encode(['error' => 'Menu addition failed.']);
                    }
                
                    $stmt->close();
                }
}
?>
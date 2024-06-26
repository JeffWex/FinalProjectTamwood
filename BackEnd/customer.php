<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include("./config.php");

if($_SERVER["REQUEST_METHOD"]=="POST") {
    switch($_POST["mode"]){

        case "all-books":
            $dbCon = new mysqli ($dbServer,$dbUser,$dbPass,$dbName);
            if($dbCon->connect_error){
                echo "Connection to DB failed! ".$dbCon->connect_error;
            } else {
                $selectCmd = "SELECT * FROM book_tb WHERE `book_tb`.`status`='Available'";
                $result = $dbCon->query($selectCmd);
                $bList = [];
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        array_push($bList, $row);
                    }
                    echo json_encode($bList);
                } else {
                    echo "No data found!";
                }
                $dbCon->close();
            }
            break;

            case "my-books":
                $dbCon = new mysqli ($dbServer,$dbUser,$dbPass,$dbName);
                if($dbCon->connect_error){
                    echo "Connection to DB failed! ".$dbCon->connect_error;
                } else {
                    $cid = $_POST["cid"];
                    $selCmd = "SELECT * FROM orderbook_tb WHERE `orderbook_tb`.`cid`='$cid'";
                    $result = $dbCon->query($selCmd);
                    $bList = [];
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            array_push($bList, $row);
                        }
                        echo json_encode($bList);
                    } else {
                        echo "No data found!";
                    }
                    $dbCon->close();
                }
                break;

            case "select-book": // CHECK INNER JOIN
                $dbCon = new mysqli ($dbServer,$dbUser,$dbPass,$dbName);
            if($dbCon->connect_error){
                echo "Connection to DB failed! ".$dbCon->connect_error;
            } else {
                $bid=$_POST["bid"];
                $cid=$_POST["cid"];
                $selectCmd = "SELECT * FROM book_tb WHERE `book_tb`.`id`='$bid'";
                $selresult = $dbCon->query($selectCmd);
                $bList = [];
                if($selresult->num_rows > 0){
                    while($row = $selresult->fetch_assoc()){
                        array_push($bList, $row);
                    }
                    foreach($bList as $book){
                        if($book["id"] == $bid) {
                            $total = $book["price"] * $book["quantity"];
                            $iquery = "INSERT INTO orderbook_tb (bid, book_name, book_author, quantity, price, cid, total) VALUES(?,?,?,?,?,?,?)";
                            $stmt = $dbCon->prepare($iquery);
                            $stmt->bind_param("issidid", $book["id"], $book["book_name"], $book["book_author"], $book["quantity"], $book["price"], $cid, $total);
                            $stmt->execute();

                            $uquery = "UPDATE book_tb SET `status`='Unavailable' WHERE id=$bid";
                            $stmt = $dbCon->query($uquery);
                            echo "Book rented";
                        } else {
                            echo "No book found";
                        }
                    }
               
                } else {
                    echo "No data found!";
                }
                $dbCon->close();
            }
                break;

            case "return-book":
                $dbCon = new mysqli ($dbServer,$dbUser,$dbPass,$dbName);
                if($dbCon->connect_error){
                    echo "Connection to DB failed! ".$dbCon->connect_error;
                } else {
                $bid = $_POST["bid"];

                $selectQuery = "DELETE FROM orderbook_tb WHERE `orderbook_tb`.`bid`= $bid";
                $data = $dbCon->query($selectQuery);

                $uquery = "UPDATE book_tb SET `status`='Available' WHERE id=$bid";
                $stmt = $dbCon->query($uquery);
                
                echo "Books returned!";
                $dbCon->close();
            }
                break;

            case "search-books":
                $dbCon = new mysqli ($dbServer,$dbUser,$dbPass,$dbName);
                if($dbCon->connect_error){
                    echo "Connection to DB failed! ".$dbCon->connect_error;
                } else {
                    $cat = $_POST["cat"];
                    // print_r($cat);
                    if($cat!="All"){
                        $selectCmd = "SELECT * FROM book_tb WHERE `book_tb`.`category`='$cat' AND `book_tb`.`status`='Available'";
                        $result = $dbCon->query($selectCmd);
                        $bList = [];
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                array_push($bList, $row);
                            }
                            echo json_encode($bList);
                        } else {
                            echo "No data found!";
                        }
                    } else {
                        $selectCmd = "SELECT * FROM book_tb WHERE `book_tb`.`status`='Available'";
                        $result = $dbCon->query($selectCmd);
                        $bList = [];
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                array_push($bList, $row);
                            }
                            echo json_encode($bList);
                        } else {
                            echo "No data found!";
                        }
                    }
                }
                $dbCon->close();
                break;
    }
}

?>
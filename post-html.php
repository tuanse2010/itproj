<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$storeImg = $_POST['storeImg'];
$storeImg = filter_input(INPUT_POST, "storeImg");

if ($storeImg == "success") {
    
//    echo json_encode(array("abc" => 'successfuly registered'));    
    $session_id = filter_input(INPUT_SERVER, "REMOTE_ADDR"); //$_SERVER ['REMOTE_ADDR'];
    // Get the data
    $imageData = filter_input(INPUT_POST, "sigData");

    // Remove the headers (data:,) part.
    // A real application should use them according to needs such as to check image type
    $filteredData = substr($imageData, strpos($imageData, ",") + 1);

    // Need to decode before saving since the data we received is already base64 encoded
    $unencodedData = base64_decode($filteredData);

    // echo "unencodedData".$unencodedData;
    $imageName = "sign_" . rand(5, 1000) . rand(1, 10) . rand(10000, 150000) . rand(1500, 100000000) . ".png";
    // Set the absolute path to your folder (i.e. /usr/home/your-domain/your-folder/
    $filepath = "images/" . $imageName;

    $fp = fopen($filepath, 'wb');
    fwrite($fp, $unencodedData);
    fclose($fp);

    // Connect to a mySQL database and store the user's information so you can link to it later
    $dsn = 'mysql:host=au-cdbr-azure-southeast-a.cloudapp.net;dbname=contribution';
    $username = 'b9838ff857bddd';
    $password = '903ada06';

    try {
        $db = new PDO($dsn, $username, $password);
        $query = "INSERT INTO `customer` VALUES (:session_id, :imageName)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':session_id', $session_id);
        $stmt->bindValue(':imageName', $imageName);
        $stmt->execute();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
        exit();
    }
}

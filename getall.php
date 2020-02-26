<?php
include 'database.php';

$sql = "SELECT * FROM guestbook ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $res = array();
    while($row = $result->fetch_assoc()) {
      $res[] = $row;
        // echo json_encode(array('Id'=>$row["id"],'Name'=>$row["name"],'Content'=>$row["content"],'Time'=>$row["time"]));
    }
    echo json_encode($res);
} else {
    echo "0 results";
}
mysqli_close($conn);
?>

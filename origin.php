<?php
header("Access-Control-Allow-Origin: *");
$serverUrl = "163.22.17.202";
$connInfo = array( "Database"=>"IOT_THU", "UID"=>"thu", "PWD"=>"thu23590121", "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverUrl, $connInfo);

if( $conn ) {
 //echo "Connection succeeded.";
 //echo "<br>";
}else{
 //echo "Connection failed.";
 die( print_r( sqlsrv_errors(), true));
}
//$result = sqlsrv_query($conn,"SELECT CAST(evTime AS DATE) [evDate], DATEPART(hour, [evTime]) [evHour], stId, srId, MAX(evValue) [evValue] FROM EnvironmentData WITH (INDEX(IDX_EnvironmentData)) WHERE stId = '$stId' AND evTime BETWEEN DATEADD(hour,-12,GETDATE()) AND GETDATE() GROUP BY CAST(evTime AS DATE), DATEPART(hour, [evTime]), stId, srId ORDER BY CAST(evTime AS DATE),DATEPART(hour, [evTime])");
$result = sqlsrv_query($conn, "SELECT TOP 1000 * FROM EnvironmentData ORDER BY evTime DESC"); //select Table_1資料表
if($result === false) {
 die( print_r( sqlsrv_errors(), true) );
}
else{
 $rrr = array();
 while( $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC) ) {
   /*
   $hours = $row['evHour'];
   $Date = $row['evDate']->format('Y-m-d H:i:s');
   $newDate = date ('Y-m-d H:i:s',strtotime("+$hours hour",strtotime($Date)));

   $rrr[$newDate][$row['srId']]=$row['evValue'];
   */
   //array_push($rrr,$row);
   //echo json_encode($row, JSON_UNESCAPED_UNICODE);
   $json[] = $row;
   //echo json_encode($json);

 }
 echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
sqlsrv_close($conn);
?>

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
$result = sqlsrv_query($conn, "DELETE FROM EnvironmentData"); //select Table_1資料表
//SELECT CAST(evTime AS DATE) [evDate], DATEPART(hour, [evTime]) [evHour], stId, srId, ROUND(MAX(evValue),2) [evValue] FROM EnvironmentData WHERE evTime BETWEEN DATEADD(hour,DATEDIFF(hour, 0, GETDATE()), 0) AND GETDATE() GROUP BY CAST(evTime AS DATE), DATEPART(hour, [evTime]), stId, srId ORDER BY CAST(evTime AS DATE),DATEPART(hour, [evTime])
if($result === false) {
 die( print_r( sqlsrv_errors(), true) );
}
else{
 echo 'Delete Successful';
}
sqlsrv_close($conn);
?>

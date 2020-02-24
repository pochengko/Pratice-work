<?php
header("Access-Control-Allow-Origin: *");
$serverUrl = "163.22.17.202";
$connInfo = array( "Database"=>"IOT_THU", "UID"=>"thu", "PWD"=>"thu23590121", "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverUrl, $connInfo);
$item = $_GET['sensorItem'];
if( $conn ) {
 //echo "Connection succeeded.";
}else{
 //echo "Connection failed.";
 die( print_r( sqlsrv_errors(), true));
}

//$result = sqlsrv_query($conn, "SELECT stId,srId,evTime,evValue FROM EnvironmentData WHERE srId = '$item' AND CONVERT(varchar(20), evTime, 120) LIKE '2017-12-%:0%:0%' ORDER BY evTime ASC"); //select Table_1資料表 WITH (INDEX(IDX_EnvironmentData))
$result = sqlsrv_query($conn,"SELECT CAST(evTime AS DATE) [evDate], DATEPART(hour, [evTime]) [evHour], stId, srId, ROUND(AVG(evValue),0) [evValue] FROM EnvironmentData WHERE srId = '$item' AND evTime BETWEEN DATEADD(hour,-12,GETDATE()) AND GETDATE() GROUP BY CAST(evTime AS DATE), DATEPART(hour, [evTime]), stId, srId ORDER BY CAST(evTime AS DATE),DATEPART(hour, [evTime])");

if($result === false) {
 die( print_r( sqlsrv_errors(), true) );
}
else{
 $rrr = array();
 while( $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC) ) {
   //$Date = $row['evTime']->format('Y-m-d H');
   //$stId = $row['stId'];
   $hours = $row['evHour'];
   $Date = $row['evDate']->format('Y-m-d H:i:s');
   $newDate = date ('Y-m-d H:i:s',strtotime("+$hours hour",strtotime($Date)));

   $TEMP = array();
   $TEMP['evValue']=$row['evValue'];
   $TEMP['srId']=$row['srId'];
   //$rrr[$Date][$stId][$row['srId']]=$row['evValue'];
   $rrr[$newDate][$row['stId']]=$TEMP;
   //$json[] = $row;


 }
 echo json_encode($rrr, JSON_UNESCAPED_UNICODE);
}
sqlsrv_close($conn);
?>

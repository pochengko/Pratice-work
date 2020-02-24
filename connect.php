<?php
header("Access-Control-Allow-Origin: *");
$serverUrl = "163.22.17.202";
$connInfo = array( "Database"=>"IOT_THU", "UID"=>"thu", "PWD"=>"thu23590121", "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverUrl, $connInfo);
//$stId = $_POST['siteName'];
if( $conn ) {
 //echo "Connection succeeded.";
 //echo "<br>";
}else{
 //echo "Connection failed.";
 die( print_r( sqlsrv_errors(), true));
}
$result = sqlsrv_query($conn,"SELECT CAST(evTime AS DATE) [evDate], DATEPART(hour, [evTime]) [evHour], stId, srId, ROUND(AVG(evValue),0) [evValue] FROM EnvironmentData WHERE evTime BETWEEN DATEADD(hour,DATEDIFF(hour, 0, GETDATE()), 0) AND GETDATE() GROUP BY CAST(evTime AS DATE), DATEPART(hour, [evTime]), stId, srId ORDER BY CAST(evTime AS DATE),DATEPART(hour, [evTime])");
//$result = sqlsrv_query($conn, "SELECT * FROM EnvironmentData WHERE stId = '$stId' AND CONVERT(varchar(20), evTime, 120) LIKE '2017-12-%:00:00' ORDER BY evTime ASC"); // WITH (INDEX(IDX_EnvironmentData)) select Table_1資料表AND evTime BETWEEN DATEADD(hour,-12,GETDATE()) AND GETDATE()evTime BETWEEN DATEADD(hour,DATEDIFF(hour, 0, GETDATE()), 0) AND GETDATE()
if($result === false) {
 die( print_r( sqlsrv_errors(), true) );
}
else{
 $rrr = array();

 while( $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC) ) {
   $hours = $row['evHour'];
   $Date = $row['evDate']->format('Y-m-d H:i:s');
   $newDate = date ('Y-m-d H:i:s',strtotime("+$hours hour",strtotime($Date)));

   $rrr[$newDate][$row['stId']][$row['srId']]=$row['evValue'];

   //$rrr2 = array('stId' => $row['stId'],'PublishTime' => $newDate);
   //$rrr2[$row['srId']]=$row['evValue'];
   //$json[] = $row;

 }
 echo "<table>";

 foreach ($rrr as $key=>$value) {
   //echo "$key <br>";

   foreach ($value as $key2=>$value2) {
     //echo "$key2";
     echo "<tr>";
     echo "<th>PublishTime</th>";
     echo "<th>stId</th>";
     ksort($value2);
     foreach ($value2 as $key3=>$value3) {
       echo "<th>$key3</th>";
     }
     echo "</tr>";
     echo "<tr>";
     echo "<td>$key</td>";
     echo "<td>$key2</td>";
     foreach ($value2 as $key3=>$value3) {
       echo "<td>$value3</td>";
     }
     echo "</tr>";
     //foreach ($value2 as $key3=>$value3) {



       //echo "<th>$key3</th>";
       /*
       echo "<tr>";
       echo "<td>$key</td>";
       echo "<td>$key2</td>";
       echo "<td>$value3</td>";
       echo "</tr>";
       */
     //}
   }
 }

 echo "</table>";
 //$rrr2=array('PublishTime' => $key, $key3 => $value3);
 echo '</br>';
 echo '</br>';
 echo json_encode($rrr, JSON_UNESCAPED_UNICODE);
 echo '</br>';
 echo '</br>';
 echo ksort($key3);
 //echo json_encode($rrr2, JSON_UNESCAPED_UNICODE);
 //echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ECharts</title>
    <!-- 引入 echarts.js -->
    <script src="https://cdn.bootcss.com/echarts/3.6.2/echarts.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery.blockUI.js"></script>
</head>

<body>
  <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
  <select name="stId" id="stId" style="font-size:25px">
    <option value="407_30001">407_30001</option>
    <option value="407_30002">407_30002</option>
    <option value="407_30003">407_30003</option>
    <option value="407_30004">407_30004</option>
  </select>

  <div id="main" class="container-fluid text-center" style="max-width:100%; height:800px;"></div>
  <script type="text/javascript">
      // 基于准备好的dom，初始化echarts实例

      var myChart;
      var dataAllMap;
      var stId;
      $(document).ready(function() {
        alert("Please select the dropdown list of siteID to view chart.(Default : 407_30001)");
        myChart = echarts.init(document.getElementById('main'));
        dataAllMap = new Map();
        $("#stId").change(function(){
          stId = document.getElementById("stId").value;
          getData(stId);
          });
         getData('407_30001');
      });

      function getData(stId) {
        $.blockUI({ message: '<img src="/img/ajax-loader.gif" />' });
          $.ajax({
                  url: "connection.php",
                  type: "POST",
                  dataType: "json",
                  data: {
                    siteName: stId
                  },
                  cache: false,
                  success: function(data) {
                    //console.log(data);
                      var HumidityArray = new Array();
                      var IlluminanceArray = new Array();
                      var PM1Array = new Array();
                      var PM10Array = new Array();
                      var PM25Array = new Array();
                      var TemperatureArray = new Array();
                      var VOCArray = new Array();
                      var dateArray = new Array();
                      $.each(data, function(key, value) {
                        $.unblockUI();
                        HumidityArray.push(value['Humidity'].toFixed(2));
                        IlluminanceArray.push((Math.sqrt(value['Illuminance'])*5).toFixed(2));
                        PM1Array.push(value['PM1']);
                        PM10Array.push(value['PM10']);
                        PM25Array.push(value['PM2.5']);
                        TemperatureArray.push(value['Temperature'].toFixed(2));
                        VOCArray.push(value['VOC']/10);
                        dateArray.push(key);

                      });
                      showData(dateArray, HumidityArray, IlluminanceArray, PM1Array, PM10Array, PM25Array, TemperatureArray, VOCArray, stId);
                  }
              });
        }

      function showData(date, Humidity, Illuminance, PM1, PM10, PM25, Temperature, VOC, stId) {
          myChart.setOption(option = {
              title: {
                  text: stId
              },
              tooltip: {
                  trigger: 'axis',
                  axisPointer: {
                      animation: false
                  }
              },
              legend: {
                  data: ['Humidity', 'Illuminance(sqrt)*5', 'PM1', 'PM10', 'PM2.5', 'Temperature', 'VOC/10']
              },
              grid: {
                x: '3%',
                x2: 30,
                y: '10%',
                y2: '10%'
              },
              toolbox: {
                  feature: {
                      mark: {
                          show: true
                      },
                      dataView: {
                          show: true,
                          readOnly: false
                      },
                      magicType: {
                          show: true,
                          type: ['line', 'bar']
                      },
                      saveAsImage: {
                          show: true
                      }
                  }
              },
              dataZoom: [{
                startValue: '2017-11-08 09:11:54'
              }, {
                type: 'inside'
              }],
              axisPointer: {
                  link: {
                      xAxisIndex: 'all'
                  }
              },
              xAxis: {
                  type: 'category',
                  boundaryGap: false,
                  data: date
              },
              yAxis: {
                  type: 'value'
              },
              series: [{
                  name: 'Humidity',
                  type: 'line',
                  data: Humidity
              },{
                  name: 'Illuminance(sqrt)*5',
                  type: 'line',
                  data: Illuminance
              },{
                  name: 'PM1',
                  type: 'line',
                  data: PM1
              },{
                  name: 'PM10',
                  type: 'line',
                  data: PM10
              },{
                  name: 'PM2.5',
                  type: 'line',
                  data: PM25
              },{
                  name: 'Temperature',
                  type: 'line',
                  data: Temperature
              },{
                  name: 'VOC/10',
                  type: 'line',
                  data: VOC
              }]
          });
          window.onresize = function() {
            myChart.resize();
          };
        }
  </script>


</body>
</html>

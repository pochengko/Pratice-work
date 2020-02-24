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
  <select name="item" id="item" style="font-size:25px">
    <option value="PM2.5">PM2.5</option>
    <option value="PM1">PM1</option>
    <option value="PM10">PM10</option>
    <option value="Illuminance">Illuminance</option>
    <option value="Humidity">Humidity</option>
    <option value="Temperature">Temperature</option>
    <option value="VOC">VOC</option>
  </select>

  <div id="main" class="container-fluid text-center" style="max-width:100%; height:800px;"></div>
  <script type="text/javascript">
      // 基于准备好的dom，初始化echarts实例

      var myChart;
      var dataAllMap;
      var item;
      $(document).ready(function() {
        alert("Please select the dropdown list of item to view chart.(Default : PM2.5)");
          myChart = echarts.init(document.getElementById('main'));
          dataAllMap = new Map();
          $("#item").change(function(){
          item = document.getElementById("item").value;
          getData(item);
          });
          getData('PM2.5');
      });

function getData(item) {
  $.blockUI({ message: '<img src="/img/ajax-loader.gif" />' });
        $.ajax({
            url: "connection_item.php",
            type: "GET",
            dataType: "json",
            data: {
              sensorItem: item
            },
            cache: false,
            success: function(data) {
              //console.log(data);
              var dateArray = new Array();
              var st1Array = new Array();
              var st2Array = new Array();
              var st3Array = new Array();
              var st4Array = new Array();
            $.each(data, function(key, value) {
              $.each(value, function(key2, value2) {
              $.unblockUI();
              if (key2 == '407_30001') {
                  st1Array.push(value2['evValue']);
              } else if (key2 == '407_30002') {
                st2Array.push(value2['evValue']);
              } else if (key2 == '407_30003') {
                 st3Array.push(value2['evValue']);
              } else if (key2 == '407_30004') {
                  st4Array.push(value2['evValue']);
              }
            });
              dateArray.push(key);
            });
            console.log(dateArray);
            showData(st1Array, st2Array, st3Array, st4Array, dateArray, item);
          }
        });
        }

      function showData(st1Array, st2Array, st3Array, st4Array, dateArray, item) {
          myChart.setOption(option = {
              title: {
                  text: item
              },
              tooltip: {
                  trigger: 'axis',
                  axisPointer: {
                      animation: false
                  }
              },
              legend: {
                  data: ['407_30001', '407_30002', '407_30003', '407_30004']
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
                startValue: '201606'
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
                  data: dateArray
              },
              yAxis: {
                  type: 'value'
              },
              series: [{
                  name: '407_30001',
                  type: 'line',
                  data: st1Array
              },{
                  name: '407_30002',
                  type: 'line',
                  data: st2Array
              },{
                  name: '407_30003',
                  type: 'line',
                  data: st3Array
              },{
                  name: '407_30004',
                  type: 'line',
                  data: st4Array
              }]
          });
          window.onresize = function() {
            myChart.resize();
          };
        }
  </script>

</body>
</html>

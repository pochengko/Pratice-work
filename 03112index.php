<!DOCTYPE html>
<html>

<head>
    <title>Insert data in MySQL database using Ajax</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
    <div style="margin: auto;width: 60%;">
        <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
        </div>
        <form id="demo" name="form1" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" placeholder="Name" name="name">
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <input type="text" class="form-control" id="content" placeholder="Leave message" name="content">
            </div>

            <input type="button" name="save" class="btn btn-primary" value="Save to database" id="butsave">
        </form>
        <p id="result"></p>
        <div id="message">
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            getMessage();
            getReplyMessage();
            $('#butsave').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: "createMessage.php",
                    dataType: "json",
                    data: {
                        name: $("#name").val(),
                        content: $("#content").val()
                    },
                    success: function (data) {

                        if (data.name) {
                            $("#demo")[0].reset();
                            $("#result").html(
                                '<font color="#007500">您的名稱為「<font color="#0000ff">' + data.name +
                                '</font>」，內容為「<font color="#0000ff">' + data.content + '</font>」！</font>');
                        } else {
                            $("#demo")[0].reset();
                            $("#result").html('<font color="#ff0000">' + data.errorMsg + '</font>');
                        }

                        $("#message").html("");
                        getMessage();
                        getReplyMessage();
                    },
                    error: function (jqXHR) {
                        $("#demo")[0].reset();
                        $("#result").html('<font color="#ff0000">發生錯誤：資料庫異常\(' + jqXHR.status + '\) </font>');
                    }
                });
            });
        });

        function getMessage() {
            $.ajax({
                type: "GET",
                url: "retrieveMessage.php",
                datatype: "json",
                success: function (data) {
                    var jsonData = JSON.parse(data);

                    for (let i = 0; i < jsonData.length; i++) {
                        var message = jsonData[i];
                        $("#message").append('<p>I am message:</p><p id="content' + message.id + '">ID:' + message.id + '<br>Name:' +
                                message.name + '<br>Content:' + message.content + '<br>Time:' + message.created + '</p>');
                        $("#message").append('<input type="button" onclick="popUpForm('+message.id+')" name="reply" class="btn btn-primary" value="Reply" id="butreply' + message.id + '">\
                        <div id="reply' + message.id + '"><p>I am reply of ID ' + message.id + ':</p></div>');
                    }

                }
            });
        }

        function getReplyMessage() {
            $.ajax({
                type: "GET",
                url: "retrieveReplyMessage.php",
                datatype: "json",
                success: function (data) {
                    var jsonData = JSON.parse(data);

                    for (let i = 0; i < jsonData.length; i++) {
                        var reply = jsonData[i];
                        $("#reply"+reply.message_id).append('<p id="replycontent' + reply.id + '">Re.ID:' + reply.id + '<br>Re.Name:' +
                        reply.name + '<br>Re.Content:' + reply.content + '<br>Re.Time:' + reply.created + '</p>');
                    }

                }
            });
        }

        function popUpForm(id) {
            $("#butreply" + id ).attr('disabled',true);
            $("#content" + id).append('<form id="reply' + id + '" name="replyform' + id + '" method="post">\
                <div class= "form-group">\
                    <label for="name">Name: </label>\
                    <input type="text" class="form-control" id="replyname' + id + '" placeholder="Name" name="replyname' + id + '">\
                </div>\
                <div class="form-group">\
                    <label for="content">Content: </label>\
                    <input type="text" class="form-control" id="replycontent' + id + '" placeholder="Leave message" name="replycontent' + id + '">\
                </div>\
                    <input type="button" onclick="sendReply('+id+')" name="replysend" class="btn btn-primary" value="Send" id="butsendreply' + id + '">\
            </form><p id="replyresult' + id + '"></p>');
        }

        function sendReply(id) {
            $.ajax({
              type: "POST",
              url: "createReplyMessage.php",
              dataType: "json",
              data: {
                  message_id: id,
                  name: $("#replyname"+id).val(),
                  content: $("#replycontent"+id).val()
              },
              success: function (data) {

                  if (data.name) {
                      $("#reply"+data.message_id)[0].reset();
                      $("#replyresult"+data.message_id).html(
                          '<font color="#007500">您的名稱為「<font color="#0000ff">' + data.name +
                          '</font>」，內容為「<font color="#0000ff">' + data.content + '</font>」！</font>');
                      $("#reply"+data.message_id).html("");
                      getReplyMessage();
                  } else {
                      $("#reply"+data.message_id)[0].reset();
                      $("#replyresult"+data.message_id).html('<font color="#ff0000">' + data.errorMsg + '</font>');
                  }

              },
              error: function (jqXHR) {
                  $("#reply"+id)[0].reset();
                  $("#replyresult"+id).html('<font color="#ff0000">發生錯誤：資料庫異常\(' + jqXHR.status + '\) </font>');
              }
          });
        }
    </script>
</body>

</html>
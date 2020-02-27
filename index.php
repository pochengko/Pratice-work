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
			<label for="email">Name:</label>
			<input type="text" class="form-control" id="name" placeholder="Name" name="name">
		</div>

		<div class="form-group">
			<label for="pwd">Content:</label>
			<input type="text" class="form-control" id="content" placeholder="Leave message" name="content">
		</div>

		<input type="button" name="save" class="btn btn-primary" value="Save to database" id="butsave">
    </form>
    <p id="result"></p>
	<div id="message"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	getMessage();
	$('#butsave').on('click', function() {
        $.ajax({
            type: "POST",
            url: "save.php",
            dataType: "json",
            data: {
                name: $("#name").val(),
                content: $("#content").val()
            },
            success: function(data) {
                if (data.name) {
                    $("#demo")[0].reset();
					$("#result").html('<font color="#007500">您的名稱為「<font color="#0000ff">' + data.name + '</font>」，內容為「<font color="#0000ff">' + data.content + '</font>」！</font>');
                } else {
                    $("#demo")[0].reset();
                    $("#result").html('<font color="#ff0000">' + data.errorMsg + '</font>');
                }
			$("#message").html("");
			getMessage();
            },
            error: function(jqXHR) {
                $("#demo")[0].reset();
                $("#result").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
            }
        });
	});
});

function getMessage() {
	$.ajax({
		type: "GET",
		url: "get.php",
		datatype: "json",
		success: function(data) {
			var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.length; i++) {
				var message = jsonData[i];
				$("#message").append('<p>ID:'+message.id+'<br>Name:'+message.name+'<br>Content:'+message.content+'<br>Time:'+message.time+'</p>');
			}
		}
	});
}
</script>
</body>
</html>
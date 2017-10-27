<?php
spl_autoload_register(function ($class_name) {
	$path = dirname(__FILE__) . '/../' . $class_name . '.php';
	if (file_exists($path))
		include $path;
});

if (array_key_exists('id', $_GET) && ($_GET['id'] != ''))
{
	$id = $_GET['id'];
}
else
{
	$id = 'default';
}

\chatbot\Chat::getInstance($id)->start();

$history = \chatbot\Chat::getInstance($id)->getHistory();
$messages = '';
foreach ($history as $message)
{
	$messages .= "<div class='message'>[" . $message->getTimeStr() . "] " . $message->getSender()->getName() . ": " . $message->getMessage() . "</div>\n";
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Chatbot</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<script type="text/javascript">

			var id = '<?php echo $id ?>';
			var wait_to_talk_min = 0;
			var wait_to_talk_max = 0;
			var wait_to_talk;

			var config = {};
			$.getJSON("config/" + id + ".json", function(data) {
				if (data.hasOwnProperty("wait_to_talk"))
				{
					wait_to_talk_min = parseInt(data.wait_to_talk.min);
					wait_to_talk_max = parseInt(data.wait_to_talk.max);
					talk_after_wait();
				}
			});

			function addMessage(sender, message)
			{
				clearTimeout(wait_to_talk);

				var dt = new Date();
				var time = adjustTime(dt.getHours()) + ":" + adjustTime(dt.getMinutes()) + ":" + adjustTime(dt.getSeconds());
				$('#history').append($("<div class='message'>").text("[" + time + "] " + sender + ": " + message));

				talk_after_wait();
			}

			function talk_after_wait()
			{
				if ((wait_to_talk_min > 0) && (wait_to_talk_max >= wait_to_talk_min))
				{
					var wait_seconds = Math.floor((Math.random() * wait_to_talk_max) + wait_to_talk_min);
					wait_to_talk = setTimeout(function() {
						sendMessage("");
					}, wait_seconds * 1000);
				}
			}

			function adjustTime(time)
			{
				if (time < 10)
					return '0' + time;

				return time;
			}

			function send(e)
			{
				if (e.keyCode != 13)
					return;

				var message = $('#new_message').val();
				$('#new_message').val("");

				sendMessage(message);
			}

			function sendMessage(message)
			{
				$.ajax({
					method: "POST",
					url: "api.php?id=" + id,
					data: '{"message": "' + message + '"}',
					contentType: "application/json; charset=utf-8",
					success: function () {
						if (message != '')
							addMessage("Utente", message);
					}
				});
			}

			function publishMessages(data)
			{
				if (typeof data != 'object')
					return;

				for (var i = 0; i < data.length; i++)
				{
					addMessage("Chatbot", data[i].message);
				}
			}

			var polling = window.setInterval(function() {
				$.ajax({
					method: "GET",
					url: "api.php?id=" + id,
					success: function (data) {
						publishMessages(data);
					},
					statusCode: {
						410: function(jqXHR)
						{
							publishMessages($.parseJSON(jqXHR.responseText));
							addMessage("CHAT", "TERMINATED");
							clearInterval(polling);
							clearTimeout(wait_to_talk);
						}
					}
				});
			}, 3000);

		</script>
	</head>
	<body>
		<div id="chat">
			<div id="history"><?php
					echo $messages;
				?></div>
			<div id="message"><input type="text" size="50" name="new_message" id="new_message" onkeypress="send(event)" /></div>
		</div>
	</body>
</html>
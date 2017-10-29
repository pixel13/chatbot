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
		<script src="js/Chat.js"></script>
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<script type="text/javascript">

			$(document).ready(function () {
				new Chat({
					id: '<?php echo $id ?>',
					history: $('#history'),
					messageInput: $('#new_message'),
					messageSubmit: $('#submit')
				});
			});

		</script>
	</head>
	<body>
		<div id="chat">
			<div id="history"><?php
					echo $messages;
				?></div>
			<div id="message"><input type="text" size="50" name="new_message" id="new_message" /><button id="submit">INVIA</button></div>
		</div>
	</body>
</html>
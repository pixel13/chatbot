<?php

spl_autoload_register(function ($class_name) {
	$path = dirname(__FILE__) . '/../' . $class_name . '.php';
	if (file_exists($path))
		include $path;
});

session_start();
if (session_status() != PHP_SESSION_NONE)
	session_destroy();

$botList = \chatbot\Util::getBotList();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Chatbot</title>
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript">
			function startChat()
			{
				var id = $('#id').val();
				if (id == '')
					return;

				$('#choice').submit();
			}

			$(document).ready(function() {
				$('#id').prop("selectedIndex", 0);
			});

		</script>
	</head>
	<body class="choice">
		<form id="choice" method="GET" action="chat.php">
			<label for="id">Con chi vuoi chattare?</label>
			<select name="id" id="id" onchange="startChat()">
				<option value="">--</option>
				<?php
					foreach ($botList as $id => $name)
					{
						echo '<option value="' . $id . '">' . $name . '</option>';
					}
				?>
			</select>
		</form>
	</body>
</html>
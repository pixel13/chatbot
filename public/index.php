<?php

spl_autoload_register(function ($class_name) {
	$path = dirname(__FILE__) . '/../' . $class_name . '.php';
	if (file_exists($path))
		include $path;
});

$botList = \chatbot\Util::getBotList();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Chatbot</title>
		<link rel="stylesheet" type="text/css" href="css/main.css">
	</head>
	<body>
		<form method="GET" action="chat.php">
			<select name="id" onchange="this.form.submit()">
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
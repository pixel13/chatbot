<?php
spl_autoload_register(function ($class_name) {
	$path = dirname(__FILE__) . '/../' . $class_name . '.php';
	if (file_exists($path))
		include $path;
});

if (!array_key_exists('id', $_GET) || ($_GET['id'] == ''))
	exit(1);
$id = $_GET['id'];

$instance = \chatbot\Chat::getInstance($id);

$instance->start();

$botName = $instance->getBotName();
$mood = $instance->getBotMood();
$avatar = $instance->getBotAvatar();
$history = $instance->getHistory();

$css = 'main.css';
if ($avatar != '')
	$css = 'custom_css.php?avatar=' . urlencode($avatar);


?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $botName ?></title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="js/Chat.js"></script>
		<link rel="stylesheet" type="text/css" href="css/<?php echo $css ?>">
		<script type="text/javascript">

			$(document).ready(function () {
				var chat = new Chat({
					id: '<?php echo $id ?>',
					history: $('#messages'),
					messageInput: $('#new_message'),
					messageSubmit: $('#submit'),
					writingMessage: $('#writing'),
					avatarSelectors: ['#avatar', '.bot:before'],
					waitToTalk: {min: 900, max: 900 },
					onNewMessage: function() {
						var history = $('#history');
						history.scrollTop(history.get(0).scrollHeight);
					}
				});

				<?php
					if (count($history) > 0)
						echo "chat.initHistory(" . json_encode($history) . ");\r\n";
				?>
			});

		</script>
	</head>
	<body>
		<div id="chat">
			<div id="header">
				<div id="avatar"><span></span></div>
				<div id="status"><span></span></div>
				<div id="name"><?php echo $botName ?></div>
				<div id="mood"><?php echo $mood ?></div>
				<div id="video"><span></span></div>
				<div id="phone"><span></span></div>
				<div id="contacts" onclick="history.back()"><span></span></div>
			</div>
			<div id="world"><span></span></div>
			<div id="history"><div id="messages"></div><div id="writing"><span><?php echo $botName ?> sta scrivendo...</span></div></div>
			<div id="new">
				<input type="text" name="new_message" id="new_message" autocomplete="off" />
				<div id="send_contacts"><span></span></div>
				<div id="emoticons"><span></span></div>
				<button id="submit"><span></span></button>
			</div>
		</div>
	</body>
</html>
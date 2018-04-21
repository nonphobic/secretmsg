<?php
$title = null;
$message = null;
$errors = [];
$success = false;
//ovo govno se povezuje na bazu
try {
		$db = new PDO ("mysql:host=localhost;dbname=database","user","password");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
       echo "E do kurca! Baza nešto jebe ale! <br>";
       echo  $e->getMessage();
   }



   if (!empty($_POST)) {
     if(!empty($_POST['title'])){
       $title = htmlentities($_POST['title']);
     } else {
       $errors['title'] = 'title is required<br>';
     }

   if (!empty($_POST)) {
     if(!empty($_POST['message'])){
       $message = htmlentities($_POST['message']);
     } else {
       $errors['message'] = 'message is required<br>';
     }
   }

   if (!count($errors)) {
	function rnd($length = 11) {
	$str = "";
	$characters = array_merge(range('a','z'), range('0','9'), array('@', "[", "]") );
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}
    $random = rnd();
    $hash ='SM_' . $random;

    $idk = $db->prepare("INSERT INTO messages (title, message, hash) VALUES (:title, :message, :hash)");
    $idk-> bindParam(':title', $title);
    $idk-> bindParam(':message', $message);
    $idk-> bindParam(':hash', $hash);
    $idk = $idk->execute();
	
	
	
	
	
	$ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('Europe/Belgrade');      
	$stamp=date("Y/m/d h:i:sa");
	$idk2 = $db->prepare("INSERT INTO logs (title, message, ip, timestamp) VALUES (:title, :message, :ip, :timestamp)");
	$idk2-> bindParam(':title', $title);
    $idk2-> bindParam(':message', $message);
	$idk2-> bindParam(':ip', $ip);
	$idk2-> bindParam(':timestamp', $stamp);
	$idk2 = $idk2->execute();
	
    if ($idk) $success = true;

   } else {
    foreach ($errors as $error) {
      echo $error;
    }

   }

 } elseif(isset($_GET['msg'])) {
   $hash = htmlentities($_GET['msg']);
   $idk = $db->prepare("SELECT title, message FROM messages WHERE hash = :hash LIMIT 1");
   $idk->bindParam(':hash', $hash);
   $idk->execute();
   $result = $idk->fetch(PDO::FETCH_ASSOC);
   $count = $idk->rowCount();

   if ($count){
	 if (strlen($hash) < 14)
{
	 $title = $result['title'];
     $message = '<div id="idkhowtonamethis">' .'<p>'. $result['message'] . '<br><br><hr><center><strong>This message is a system notice</strong></center></p></div>';
}

elseif	(strlen($hash) == 14)
{
		$title = $result['title'];
		$message = '<div id="idkhowtonamethis">' .'<p>'. $result['message'] . '<br><br><hr><center><strong>Btw, this message already have been deleted from the database! Press CTRL + F5 to clear local cache.</strong></center></p></div>';
		$idk = $db->prepare("DELETE FROM messages WHERE hash = ?")->execute([$hash]);
   }} else {
     $title ='Fuyukai desu...';
     $message = '<p id="idkhowtonamethis"> How unpleasant... this message have been removed, or maybe it never existed? </p><img src="fuyukai.gif">';

   }
 }
 ?>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
	<link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
	<link rel="icon" href="favicon.gif" type="image/gif" sizes="16x16">
	<title>girly.moe</title>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
<script>
function validateForm() {
    var x = document.forms["code"]["msg"].value;
    if (x == "") {
	document.getElementById("ohno").innerHTML = "Looks like you forgot to paste your code.";
    return false;
    }
}

function validate() {
    var x = document.forms["send"]["title"].value;
	var y = document.forms["send"]["message"].value;
    if (x == "") {
	document.getElementById("ohno1").innerHTML = "You forgot to fill the 'title' field.";
    return false;
    }
	
	if (y == "") {
	document.getElementById("ohno2").innerHTML = "You forgot to fill the 'message' field.";
    return false;
    }
}
</script>
</head>
<body>
			<div id="card">
			<!---- <div class="corner"><img src="cutie.png"></div> ---->
			<div id="content">
			<div id="title">
			<h1>Girly.moe</h1>
			</div>
			<hr>
			<h1 style="text-align:right; font-size: 12pt;";><a href="index.html" id="idkhowtonamethis2">Home</a><a href="secret.php" id="idkhowtonamethis">Secret msg</a><a href="blog.html" id="idkhowtonamethis2">Blog</a></h1>
			<hr>
      <?php
      if(isset($_GET['msg'])){
      ?>
      <h1><?=$title?></h1>
      <?=$message?>
    <?php }else { ?>
			<?php
        if (!$success) {
      ?>
      <h1>secret message</h1>
	  <?php unset($_POST); ?>
      <form id="idkhowtonamethis"  action="" onsubmit="return validate()" name="send" method="post" >
		<p style="color:red; background-color: #111;border-radius: 5px; padding: 5px;"> <strong>By submitting you agree with my</strong> <a href="?msg=privacy">privacy policy</a></p>
		<p style="color:red; background-color: #111;border-radius: 5px; padding: 5px;"> <strong>Since y'all don't trust me check</strong> <a href="https://github.com/nonphobic/secretmsg">the source code</a></p>
        <p id="ohno1" style="color:white; background-color: #ba013d;border-radius: 5px;"></p>
        <p id="ohno2" style="color:white; background-color: #ba013d;border-radius: 5px;"></p>
        <p><input class="he" type="text" placeholder="Title of your message" name="title" value="<?=$title?>"></p>
        <p><textarea class="he" rows="5" cols="50" placeholder="Type your message here" name="message" value="<?=$message?>"></textarea></p>
        <p><button type="submit" class="he">Send</button></p>
		<p>When you press 'Send', you will get an unique code that can be used only <strong>ONCE</strong>.</p>
      </form >

	  <form id="idkhowtonamethis" action="" onsubmit="return validateForm()" name="code">
	  <p style="color:white; background-color: #ba013d;border-radius: 5px;">Use code "test" (without quotes), to test the system</p>
	  <p>Recieved a message? paste your code here!</p>
	  <p><input class="he" type="text" placeholder="enter code here" name="msg"><button type="submit" class="he">read the message!</button></p>
	  <p id="ohno" style="color:white; background-color: #ba013d;border-radius: 5px;"></p>
	  </form>
    <?php }
    else { ?>
      <h1>Code generated!</h1>
      <p id="idkhowtonamethis">Your secret message is now inserted into the database!<br><br>
	  Give this code to your friend: <span style="font-size: 24pt;"><?=$hash?></span><br><br></p>

  <?php  } ?>
  <?php  } ?>

			</div>
			</div>
</body>
</html>

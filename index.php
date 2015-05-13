<?
session_start();

$OWNER = 'egonelbre@gmail.com';
$SSL_VERIFY = false;

switch($_SERVER['REQUEST_URI']){
	case '/':       index();  return;
	case '/login':  login();  return;
	case '/logout':	logout(); return;
}

http_response_code(404);
exit();

function user(){
	return isset($_SESSION['user']) ? $_SESSION['user'] : '';
}

function login(){
	$result = PersonaVerify();

	if($result['status'] !== 'okay'){
		http_response_code(403);
		return;
	}

	global $OWNER;
	if($result['email'] == $OWNER) {
		$_SESSION['logged'] = true;
		$_SESSION['user'] = $result['email'];
	}
}

function logout(){
	session_destroy();
}

function PersonaVerify(){
	$url = 'https://verifier.login.persona.org/verify';

	$assertion = filter_input(
  		INPUT_POST,
        'assertion',
        FILTER_UNSAFE_RAW,
        FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
	);

	$scheme = 'http';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "on") {
		$scheme = 'https';
	}

	$audience = $scheme . '://' . $_SERVER['HTTP_HOST'];
	$data = 'assertion=' . urlencode($assertion) . '&audience=' . urlencode($audience);

	global $SSL_VERIFY;
	$params = array(
		'http' => array(
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'method' => 'POST',
			'content' => $data
		),
		'ssl' => array(
			'verify_peer' => $SSL_VERIFY,
			'verify_host' => $SSL_VERIFY
		)
	);
	$context = stream_context_create($params);
	$result = file_get_contents($url, false, $context);
	return json_decode($result, true);
}

function index(){
?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>Ramblings</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.ico">

	<link rel="stylesheet" type="text/css" href="/client/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/client/css/header.css">
	<link rel="stylesheet" type="text/css" href="/client/css/pages.css">

	<script src="https://login.persona.org/include.js"></script>
	<script src="/client/persona.js"></script>
</head>
<body>
	<div id="header">
		<a class="element" href="/" title="Home">Ramblings</a>
		<form class="search element">
			<input id="query" placeholder="Search..."></input>
			<button id="search-button" class="search-icon" type="submit" tabindex="-1">&#128269;</button>
		</form>
		<a id="signin" class="element" href="#">&#128274;</a>
		<a id="signout" class="element hidden" href="#">&#128274;</a>
	</div>
</body>
<? } ?>
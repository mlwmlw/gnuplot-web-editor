<?php
	session_start();
	if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
	}
	if(isset($_GET['action'])) {
		if($_GET['action'] == 'clean') {
			session_destroy();
			header('Location: index.php');
		}
	}
		
	if(empty($_SESSION['code'])) {
		$_SESSION['code'] = array();
	}
	
	
	if(isset($_GET['code']) && isset($_SESSION['code'][$_GET['code']])) {
		$code = $_SESSION['code'][$_GET['code']];
	}
	else {
		$code = isset($_POST['code']) ? $_POST['code'] : end($_SESSION['code']);
	}
	
	if(isset($_POST['code'])) {
		array_push($_SESSION['code'], $code);
	}
	
	if(!$code) {		
		$code = file_get_contents("./example/default.plot");		
	}
	$name = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)), 0, 5);
	$file = "/tmp/{$name}.plot";
	file_put_contents($file, "set terminal png size 600,400 medium\nset output \"/tmp/{$name}.png\"\n" . $code);
	$output = shell_exec("/usr/local/bin/gnuplot {$file} 2>&1");
	if(strpos($output, 'invalid') !== FALSE) {
		$code .= "\n {$output}" ;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Gnuplot Online</title>		
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="./assets/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="./assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      footer {
      	text-align:center;
      }
    </style>
    <script src="assets/CodeMirror-2.32/lib/codemirror.js"></script>
		<link rel="stylesheet" href="assets/CodeMirror-2.32//lib/codemirror.css">
		<script src="assets/CodeMirror-2.32/mode/shell/shell.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>
			$(document).ready(function() {
				var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("code"), {
        	lineNumbers: true
      	});
      	
      	$('.sidebar-nav a').click(function() {
      		$.get('./example/' + $(this).text() + '.plot', function(res) {
      			myCodeMirror.setValue(res);
      		});
      	});
			});
		</script>
		
	</head>
	<body>	
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">Gnuplot </a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>

              <li class="dropdown">
	              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Histroy <b class="caret"></b></a>
	              <ul class="dropdown-menu">
									<? foreach($_SESSION['code'] as $k => $c): ?>	               
	                	<li><a href="?code=<?=$k?>"><?=$k?></a></li>
	                <? endforeach; ?>
	              </ul>
	            </li>
              <li><a href="?action=clean">Clean</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
		
		<div class="container-fluid">
 			<div class="row-fluid">
				<div class="span2">
					 <div class="well sidebar-nav">
	            <ul class="nav nav-list">
	              <li class="nav-header">Example</li>
	              <li><a href="javascript:void(0)">line</a></li>
	              <li><a href="javascript:void(0)">histogram</a></li>
	            </ul>
	          </div><!--/.well -->
				</div>
				<div class="span8">
					<img src="./img.php?name=<?=$name?>">
			    <form method="POST">
						<textarea id="code" name="code"><?=$code?></textarea>
						<br />
						<center><input class="btn btn-primary" type="submit" value="Plot" /></center>
					</form>
				</div>
			</div>
			<footer>
		    <p>&copy; Antslab 2012</p>
		  </footer>
		</div> <!-- /container -->
	
    <script src="./assets/bootstrap/js/bootstrap-dropdown.js"></script>

	</body>
</html>
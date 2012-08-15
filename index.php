<?php
	include('./config.php');
	session_start();
	if(isset($_GET['action'])) {
		if($_GET['action'] == 'clean') {
			session_destroy();		
		}
		else if($_GET['action'] == 'save' && isset($_POST['name']) && preg_match('/^[A-Za-z0-9-]+$/', $_POST['name'])){
			file_put_contents('./record/' . $_POST['name']. '.plot', end($_SESSION['code']));
		}	
		header('Location: index.php');
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
	$name = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, 12);
	$file = "/tmp/{$name}.plot";
	
	//png pngcairo
	file_put_contents($file, "
set terminal png size 600,400 font 'Microsoft JhengHei,10'
set output \"/tmp/{$name}.png\"
" . $code);

	$error = shell_exec("{$config['gnuplot']} {$file} 2>&1");
	unlink($file);	
	
	$examples = scan('./example');
	$records = scan('./record');
	function scan($path) {
		$dir = scandir($path);
		foreach($dir as $k => $file) {
			if($file == 'default.plot' || $file == '.' || $file == '..' || strpos($file, '.') === 0) {
				unset($dir[$k]);
			}
			else {
				$dir[$k] = str_replace('.plot', '', $file);
			}
		}
		return $dir;
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
      		$.get($(this).attr('href'), function(res) {
      			myCodeMirror.setValue(res);
      		});
					return false;
      	});

      	$('.save').click(function() {
					$('#saveModal').modal('show');
					return false;
				});
				$('#saveModal .btn-save').click(function() {
					$('#saveModal form').submit();
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
              <li class="active"><a href="index.php">Home</a></li>

              <li class="dropdown">
	              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Histroy <b class="caret"></b></a>
	              <ul class="dropdown-menu">
									<? foreach($_SESSION['code'] as $k => $c): ?>	               
	                	<li><a href="?code=<?=$k?>"><?=$k?></a></li>
	                <? endforeach; ?>
	              </ul>
	            </li>
	            <li><a class="save" href="?action=save">Save</a></li>
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
	              <? foreach($examples as $example): ?>
	              	<li><a href="./example/<?=$example?>.plot"><?=$example?></a></li>
	              <? endforeach; ?>
	              <li class="nav-header">Record</li>
	              <? foreach($records as $record): ?>
	              	<li><a href="./record/<?=$record?>.plot"><?=$record?></a></li>
	              <? endforeach; ?>

	            </ul>
	          </div><!--/.well -->
				</div>
				<div class="span8">
					<? if($warning): ?>
						<div class="alert alert-warning">
							<?=$warning?>
						</div>
					<? endif;?>

					<? if($error): ?>
						<div class="alert alert-error">
							<?=$error?>
						</div>
					<? endif;?>
					<img src="./img.php?name=<?=$name?>">
			    <form method="POST" action="./index.php">
						<textarea id="code" name="code"><?=$code?></textarea>
						<br />
						<center><input class="btn btn-primary" type="submit" value="Plot" /></center>
					</form>
				</div>
			</div>
			<footer>
		    <p>&copy; <a href="http://mlwmlw.org">mlwmlw.org</a> 2012</p>
		  </footer>
		</div> <!-- /container -->
		<div class="modal hide" id="saveModal"> 
  <div class="modal-header"> 
    <button type="button" class="close" data-dismiss="modal">×</button> 
    <h3>save gnuplot</h3> 
  </div> 
  
  <div class="modal-body"> 
	    <form class="well" method="post" action="?action=save"> 
	      <div class="control-group"> 
		    	<label class="control-label">Name</label> 
				  <input type="text" name="name" class="input" placeholder="file name"> 
			  </div> 
			</form> 
	  </div> 
	  <div class="modal-footer"> 
	    <a href="javascript:void(0)" class="btn" data-dismiss="modal">Cencel</a> 
	    <a href="javascript:void(0)" class="btn btn-primary btn-save">Save</a> 
	  </div> 
	</div>
    <script src="./assets/bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="./assets/bootstrap/js/bootstrap-modal.js"></script>

	</body>
</html>

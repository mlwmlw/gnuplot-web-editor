<?

if(isset($_GET['name'])) {
	$name = $_GET['name'];
	$file = "/tmp/{$name}.png";
	if(file_exists($file) and preg_match('/^[A-Za-z0-9-]+$/', $name)) {
		header('Content-Type: image/png');
		echo file_get_contents($file);
	}
}
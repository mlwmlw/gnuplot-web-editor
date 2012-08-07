<?

if(isset($_GET['name'])) {
	$name = $_GET['name'];
	$file = "/tmp/{$name}.png";
	if(file_exists($file) and preg_match('/^[A-Za-z0-9-]+$/', $name)) {
		
		$img = file_get_contents($file);
	}
}
header('Content-Type: image/png');
if($img) {
	echo $img;
}
else {
	$im = imagecreatetruecolor(600, 200);
	$black = imagecolorallocate($im, 0, 0, 0);
	$white = imagecolorallocate($im, 255, 255, 255);
	
	imagefilledrectangle($im, 0, 0, 599, 199, $white);
	imagettftext($im, 30, 0, 200, 100, $black, 'arial.ttf', "plot error");
	imagepng($im);
	imagedestroy($im);
}
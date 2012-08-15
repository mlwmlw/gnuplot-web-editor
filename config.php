<?php
$config = array();
$config['gnuplot'] = exec('export PATH=$PATH:/usr/local/bin/;which gnuplot 2> /dev/null');
$config['dot'] = exec('export PATH=$PATH:/usr/local/bin/;which dot 2> /dev/null');
if(!$config['gnuplot']) {
	echo 'gnuplot not found';
	exit;
}
//check perms
$stat = stat('./record');
if(!($stat['mode'] & 000002)) {
	$warning = 'Does not have permission to write to the directory(./record)';
}

if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}


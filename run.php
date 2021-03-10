<?php
error_reporting(E_ERROR | E_PARSE);
error_reporting(0);
echo shell_exec('git pull 2>&1');

#echo "<pre>$output</pre>";
#phpinfo();
?>

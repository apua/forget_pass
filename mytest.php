<?php

session_start();

if(array_key_exists('a',$_GET)) {
  $_SESSION['a']=1;
  print 'set session';
}
else {
  session_destroy();
  print 'session_destroy';
}


?>

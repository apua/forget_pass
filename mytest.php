<?php

function a() {
   $GLOBALS['c'] = 'zcxv';
}

function b() {
  echo 123;
}

if(a()==True) echo 't';
if(a()==False) echo 'f';
if(a()===False) echo '=';
if(a()===NULL) echo 'n';
echo $c;
?>

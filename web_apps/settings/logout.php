<?php

session_start();
require_once('../template.php');


session_destroy();
logout_view();

function logout_view() {

    if( array_key_exists('redirect', $_GET) )
        return logout_form($_GET['redirect']);
    return logout_form('.');
    
}

function logout_form($url) {

    above();
    ?>
    <div class="form-signin">
        <p>您已登出</p>
        <a href='<?echo $url?>'>回到上一頁</a>
    </div>
    <?
    below();

}

?>

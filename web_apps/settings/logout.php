<?php

session_start();

session_destroy();
logout_view();

function logout_view() {

    if( array_key_exists('redirect', $_GET) )
        return logout_form($_GET['redirect']);
    return logout_form('.');
    
}

function logout_form($url) {

    ?>
    <p>您已登出</p>
    <a href='<?echo $url?>'>回到上一頁</a>
    <?

}

?>

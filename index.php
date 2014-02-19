<?php

session_start();

if( array_key_exists('timeout', $_SESSION) &&
    $_SESSION['timeout'] + 10 < time() )
    session_unset();

if( array_key_exists('username', $_SESSION) )
    set_password_view();
else
    authenticate_view();


// set_password_process
///////////////////////

function set_password_view() {

    $username = $_SESSION['username'];
    if( array_key_exists('password', $_POST)===False || 
        array_key_exists('confirm_password', $_POST)===False ||
        $_POST['password']!==$_POST['confirm_password']) {
        set_password_render($username, 'You should enter password and confirm.');
    }

    $password=$_POST['password'];
    if( set_password($username,$password)===False )
        set_password_render($username, 'Set password failed, please try again later or contact admin.');

    session_destroy();
    success_render();
}


// authenticate_view
////////////////////

function authenticate_view() {

    if( array_key_exists('username', $_POST)===False )
        authenticate_render(NULL,NULL,NULL,NULL);

    $username = $_POST['username']; 
    if( array_key_exists('question', $_POST)===False ) {
        if( ($question=get_question($username))===NULL ) 
            authenticate_render($username,NULL,NULL,'Question doesn`t exist.');
        else
            authenticate_render($username,$question,NULL,NULL);
    }

    $question = $_POST['question'];
    if( array_key_exists('answer', $_POST)===False )
        authenticate_render($username,$question,NULL,NULL);
                            
    $answer = $_POST['answer'];
    if( check_answer($username,$answer)===False )
        authenticate_render($username,$question,NULL,'Answer is wrong.');
    else {
        $_SESSION['username'] = $username;
        $_SESSION['timeout'] = time();
        set_password_render($username, '');
    }
}


// templates (renders)
//////////////////////

function set_password_render($username, $alert) {

?>
<?if ($alert!==NULL) {?><p style="color: red;"><?echo $alert?></p><?}?>
<form method="post">
    <label>Set Password of <?echo $username?></label>
    <input name="password" />
    <label>Confirm Password</label>
    <input name="confirm_password" />
    <input type="submit" value="Submit" />
</form>
<?

exit();

}


function authenticate_render($username, $question, $answer, $alert) {

// 應該要避免 HTML injection 

if ($alert!==NULL) echo '<p style="color: red;">'.$alert.'</p>';

?>
<form method="post">
    <? if ($question===NULL) { ?>
    <label>Username</label>
    <input name="username" value="<?echo $username?>" />
    <? } else { ?>
    <label><?echo $question.' of '.$username?></label>
    <input name="answer" />
    <input type=hidden name="username" value="<?echo $username?>" />
    <input type=hidden name="question" value="<?echo $question?>" />
    <?}?>

    <input type="submit" value="Submit" />
</form>
<?

if ($question!==NULL) echo '<a href=".">It is not my username.</a>';

exit();

}


function success_render() {

echo 'success_render';

exit();

}


// methods
//////////

function get_data($username) {
    // add connect database check and query check in the future
    require('../config.inc.php');
    $linker = mysql_connect($mysqlauth_server,
                            $mysqlauth_acct_admin,
                            $mysqlauth_pass_admin);
    $query = "SELECT * FROM qa WHERE username='$username';";
    $result = mysql_db_query($mysqlauth_db, $query, $linker);
    if( $row=mysql_fetch_array($result) )
        return $row;
    else
        return NULL;
}


function get_question($username) {
    if( $row=get_data($username) )
        return $row['question'];
    return NULL;
}


function check_answer($username, $answer) {
    if( $row=get_data($username) )
        return $row['answer']===$answer;
    return NULL;
}


function set_password($username, $password) {
    return True;
}

?>

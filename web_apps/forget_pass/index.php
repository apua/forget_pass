<?php

session_start();
require('../template.php');

if( array_key_exists('timeout', $_SESSION) &&
    $_SESSION['timeout'] + 60 < time() )
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

    if( ! array_key_exists('username', $_POST) )
        authenticate_render(NULL,NULL,NULL,NULL);

    $username = $_POST['username']; 
    if( ! array_key_exists('question', $_POST) ) {
        $question = get_question($username);
        if( ! ($question) ) 
            authenticate_render($username,NULL,NULL,'Question doesn`t exist.');
        else
            authenticate_render($username,$question,NULL,NULL);
    }

    $question = $_POST['question'];
    if( ! array_key_exists('answer', $_POST) )
        authenticate_render($username,$question,NULL,NULL);
                            
    $answer = $_POST['answer'];
    if( ! check_answer($username,$answer) )
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

    above();
    ?>
    <form class="form-signin" method="post">
        <? if($alert) {
            ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Warning:</strong> <?echo$alert?>
            </div>
            <?
        } ?>
        <h2>Reset Password</h2>
        <h4>User: <?echo $username?></h4>
        <input type="password" class="input-block-level"
               name="password" placeholder="Password">
        <input type="password" class="input-block-level"
               name="confirm_password" placeholder="Repeat password again">
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
    <?
    below();
    exit();

}


function authenticate_render($username, $question, $answer, $alert) {

// 應該要避免 HTML injection 

    above();
    ?>
    <form class="form-signin" method="post">
        <? if($alert) {
            ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Warning:</strong> <?echo$alert?>
            </div>
            <?
        } ?>
        <? if ($question===NULL) { ?>
            <input type="text" name="username"
                   class="input-block-level" placeholder="Username" />
        <? } else { ?>
            <h2>User: <?echo$username?></h2>
            <h4>Question: <?echo$question?></h4>
            <input type="text" name="answer"
                   class="input-block-level" placeholder="The answer of question" />
            <input type=hidden name="username" value="<?echo $username?>" />
            <input type=hidden name="question" value="<?echo $question?>" />
        <?}?>
        <button class="btn btn-primary" type="submit">Continue</button>
    </form>
    <?
    below();
    exit();

    if ($question!==NULL) echo '<a href=".">It is not my username.</a>';

}


function success_render() {

    above();
    ?>
    <div class="form-signin">
        <p>變更成功, 請至 webmail 登入以測試新密碼</p>
        <a href="http://fwebmail.nctu.edu.tw">連結至 webmail</a>
    </div>
    <?
    below();
    exit();

}


// methods
//////////

function get_data($username) {
    // add connect database check and query check in the future
    require('config.inc.php');
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
    $format = '/usr/local/bin/sudo ./chpass.sh %s %s 2>&1';
    $cmd = sprintf($format, $username, escapeshellarg($password));
    exec($cmd, $output, $return_var);
    if( $return_var==0 )
        return True;
    return False;
}

?>

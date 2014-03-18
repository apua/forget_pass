<?php

//// main

session_start();
require_once('../template.php');

if( array_key_exists('username', $_SESSION) )
    settings_view();
else
    login_view();

exit();

////

function login_view() {

    if( empty($_POST) )
        return login_form(NULL);

    if( ! array_key_exists('username', $_POST) ||
        $_POST['username']=='' ||
        ! array_key_exists('password', $_POST) ||
        $_POST['password']=='')
        return login_form('please enter username and password');

    if( ! $tmp=login($_POST['username'],$_POST['password']) )
        return login_form('login failed');

    $_SESSION['username'] = $_POST['username'];
    $_SESSION['timeout'] = time();

    return self_redirect();

}

function settings_view($data=NULL,$remind=NULL,$alert=NULL) {

    if( empty($_POST) ) {
        if( ! fulfill($data=get_cleaned_data($_SESSION['username'])) )
            $remind = 'fill the form please';
    } else {
        if( ! fulfill($data=get_post_data($_POST)) )
            $alert = 'fill the form please';
        else {
            save_data($_SESSION['username'],$data);
            $remind = 'save completed';
        }
    }

    return settings_form($data,$remind,$alert);

}
    
////

function settings_form($data,$remind,$alert) {

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
        <? if($remind) {
            ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Notice:</strong> <?echo$remind?>
            </div>
            <?
        } ?>
        <h2>User: <?echo$_SESSION['username']?></h2>
        <input type="text" name="staffnum" value="<?echo $data['staffnum']?>"
               class="input-block-level" placeholder="Staff number" />
        <input type="text" name="realname" value="<?echo $data['realname']?>"
               class="input-block-level" placeholder="Full name"/>
        <input type="text" name="question" value="<?echo $data['question']?>"
               class="input-block-level" placeholder="Set a question"/>
        <input type="text" name="answer"   value="<?echo $data['answer']?>" 
               class="input-block-level" placeholder="Set an answer of the question"/>
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="logout.php" style="float:right;">logout</a>
    </form>
    <?
    below();

}

function self_redirect() {

    // it should be modified as production
    header('Location: .', True, 303);

}

function login_form($alert) {

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
        <h2>Please sign in</h2>
        <input type="text" class="input-block-level"
               name="username" placeholder="Username">
        <input type="password" class="input-block-level"
               name="password" placeholder="Password">
        <button class="btn btn-primary" type="submit">Sign in</button>
    </form>
    <?  
    below();
    
}

////

function fulfill($data) {
    foreach ($data as $v)
        if( ! $v )
            return False;
    return True;
}

function get_post_data($post) {
    $data = array();
    foreach (array('staffnum','realname','question','answer') as $key)
        $data[$key] = array_key_exists($key, $post) ? $post[$key] : '';
    return $data;
}

function get_cleaned_data($username) {
    $row = get_data($username);
    $data = array();
    foreach (array('staffnum','realname','question','answer') as $key)
        $data[$key] = htmlspecialchars($row[$key]);
    return $data;
} 

function get_data($username) {
    // add connect database check and query check in the future
    require('config.inc.php');
    $linker = mysql_connect($mysqlauth_server,
                            $mysqlauth_acct_admin,
                            $mysqlauth_pass_admin); 
    $query = "SELECT * FROM mail.qa WHERE username='$username';";
    $result = mysql_query($query, $linker);
    if( $row=mysql_fetch_array($result) )
        return $row;
    else
        return NULL;
}

function save_data($username,$data) {

    require('config.inc.php');
    $linker = mysql_connect($mysqlauth_server,
                            $mysqlauth_acct_admin,
                            $mysqlauth_pass_admin);

    $staffnum = mysql_real_escape_string($data['staffnum']);
    $realname = mysql_real_escape_string($data['realname']);
    $question = mysql_real_escape_string($data['question']);
    $answer   = mysql_real_escape_string($data['answer']);

    if( get_data($username) )
        $query = "UPDATE mail.qa
                  SET staffnum='$staffnum',realname='$realname',question='$question',answer='$answer' 
                  WHERE username='$username';";
    else
        $query = "INSERT INTO mail.qa (username,staffnum,realname,question,answer)
                  VALUES ('$username','$staffnum','$realname','$question','$answer');";
    
    mysql_query($query, $linker);   
    
}

function login($username,$password) {
    if( ! $fs=fsockopen('140.113.2.72',110,&$errono,&$errstr,5) )
        // should render warning message
        return False;

    if( strpos($msg=fgets($fs,256),'+OK')!==0 )
        //+OK Dovecot ready.
        return False;
    fputs($fs, "user $username\r\n");
    if( strpos($msg=fgets($fs,256),'+OK')!==0 )
        return False;
    fputs($fs, "pass $password\r\n");
    if( strpos($msg=fgets($fs,256),'+OK')!==0 )
        return False;
    fputs($fs, "quit \r\n");
    fclose($fs);
    return True;

}

?>



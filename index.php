<?php

session_start();

if( array_key_exists('username', $_SESSION) )
    set_password_view();
else
    authenticate_view();


// set_password_process
///////////////////////

function set_password_view() {
    if( array_key_exists('password', $_POST)===False || 
        array_key_exists('confirm_password', $_POST)===False ||
        $_POST['password']!==$_POST['confirm_password']) {
        set_password_render('You should enter password and confirm.');
    }
    else {
        $username = $_SESSION['username'];
        $password=$_POST['password'];
        if( set_password($username,$password)===False )
            set_password_render('Set password failed, please try again later or contact admin.');
        else
            session_unset();
            success_render();
 
        
    // set_password
    // if success:
    //     clean session
    //     response success page
    // else:
    //     render set password page and alert something is wrong
    }
}


// authenticate_view
////////////////////

function authenticate_view() {
    if( array_key_exists('username', $_POST)===False )
        authenticate_render(NULL,NULL,NULL,NULL);
    else {
        $username = $_POST['username']; 
        if( array_key_exists('question', $_POST)===False ) {
            if( ($question=get_question($username))===NULL ) 
                authenticate_render($username,NULL,NULL,'Question doesn`t exist.');
            else
                authenticate_render($username,$question,NULL,NULL);
        }
        else {
            $question = $_POST['question'];
            if( array_key_exists('answer', $_POST)===False )
                authenticate_render($username,$question,NULL,NULL);
                                    
            else {
                $answer = $_POST['answer'];
                if( check_answer($username,$answer)===False )
                    authenticate_render($username,$question,NULL,'Answer is wrong.');
                else
                    $_SESSION['username'] = $username;
                    set_password_render('');
            }
        }
    }
}


// templates (renders)
//////////////////////

function set_password_render($alert) {

?>

<?if ($alert!==NULL) {?><p style="color: red;"><?echo $alert?></p><?}?>
<form method="post">
    <label>Set New Password</label>
    <input name="password" />
    <label>Password Confirm</label>
    <input name="confirm_password" />
    <input type="submit" value="Submit" />
</form>
<?

}


function authenticate_render($username, $question, $answer, $alert) {

// 應該要避免 HTML injection 
?>
<?if ($alert!==NULL) {?><p style="color: red;"><?echo $alert?></p><?}?>
<form method="post">
    <label>Username</label>
    <input name="username" value="<?echo $username?>" />
    <?if ($question!==NULL) {?>
    <label><?echo $question?></label>
    <input name="answer" />
    <?}?>
    <input type="submit" value="Submit" />
    <input type=hidden name="question" value="<?echo $question?>" />
</form>
<?

}


function success_render() {
}


// methods
//////////

function get_question($username) {
    //return NULL;
    return "My Question";
}


function check_answer($username, $answer) {
    //return False; 
    return True;
}

function set_password($username, $password) {
    return True;
}

?>
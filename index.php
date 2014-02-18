<?php

session_start();

if( array_key_exists('username', $_SESSION) )
    set_password_view();
else
    authenticate_process();


// set_password_process
///////////////////////

function set_password_process($alert_msg=NULL) {
    // set_password
    // if success:
    //     clean session
    //     response success page
    // else:
    //     render set password page and alert something is wrong
}


// authenticate_process
///////////////////////

function authenticate_view() {
    if( array_key_exists('username', $_GET)===False )
        authenticate_render();
    else {
        $username = $_GET['username']; 
        if( array_key_exists('question', $_GET)===False ) {
            if( ($question=get_question($username))===NULL ) 
                authenticate_render($username=$username,
                                    $alert='Question doesn`t exist.')
            else
                authenticate_render($username=$username,
                                    $question=$question)
        }
        else {
            $question = $_GET['question'];
            if( array_key_exists('answer', $_POST)===False )
                authenticate_render($username=$username,
                                    $question=$question)
            else {
                if( check_answer($username=$username,$answer=$answer)===False )
                     authenticate_render($username=$username,
                                         $question=$question,
                                         $alert='Answer is wrong.')
                else
                    echo 'done';
                    // set session
                    // render set password page
        }
    }
}

?>

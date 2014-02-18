<?php


/////////////
// main logic
/////////////


/////////
// global
/////////

function alert($message) {
    echo '<p style="color:red;">'.$message.'</p>';
}


///////
// main
///////

if ( array_key_exists('username', $_POST)==False )
    return_input_user_page();


$username = $_POST['username'];
$QA = get_question_from_database($username);
$question = $QA[0];
$answer = $QA[1];
if ( !$question )
    return_input_user_page($error='There is no question/answer of the user.');

if ( array_key_exists('answer', $_POST)==False )
    return_user_and_question_page();

if ( check_answer_from_database($_POST['username'], $_POST['answer'])==False )
    return_user_and_question_page($error='The answer of the question is wrong.');

if ( array_key_exists('password', $_POST)==False )
    return_set_password_page();

if ( set_password($_POST['username'], $_POST['password'])==False )
    return_set_password_page($error="Something wrong thus set password failed.");

return_set_password_successfully_page();


//////////
// methods
//////////

function get_question_from_database($user) {
}

function check_answer_from_database($user, $ans) {
}

function set_password($user, $pass) {
}


////////
// pages
////////

function return_input_user_page($error=NULL) {

?>
<? if($error) alert($error); ?>
<form method="post">
  <label>Please Enter Your Faculty Username</label>
  <input name="username" placeholder="UserName" />
  <input type="submit" value="Submit" />
</form>
<?
      
}

function return_user_and_question_page($error=NULL) {

$username = $GLOBALS["username"];
$question = $GLOBALS["question"];

?>
<? if($error) alert($error); ?>
<form method="post">
  <label>Please Enter Answer the Question: <?echo $question?></label>
  <input type="hidden" name="username" value="<?echo $username?>" />
  <input               name="username" value="<?echo $username?>" />
  <input type="submit" value="Submit" />
</form>
<?

}

function return_set_password_page($error=NULL) {

?>
<? if($error) alert($error); ?>
<form method="post">
  <label>Please Enter Your Faculty Username</label>
  <input name="username" placeholder="UserName" />
  <input type="submit" value="Submit" />
</form>
<?

}

function return_set_password_successfully_page($error=NULL) {

?>
<? if($error) alert($error); ?>
<form method="post">
  <label>Please Enter Your Faculty Username</label>
  <input name="username" placeholder="UserName" />
  <input type="submit" value="Submit" />
</form>
<?

}


?>

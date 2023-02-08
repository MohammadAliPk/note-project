<?php
require_once("db.php");
session_start();


// message
function setMessage($message)
{
    $_SESSION['message'] = $message;
}
function showMessage()
{
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-warning m-3'>" . $_SESSION['message'] . "</div>";
    }
    unset($_SESSION['message']);
}


// add new users

if (isset($_POST['do-register'])) {
    $displayname = $_POST['display-name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passConf = $_POST['pass-conf'];

    $check_username = mysqli_query($db, "SELECT * FROM users WHERE username='$username'");


    if (mysqli_num_rows($check_username) > 0) {
        setMessage('نام کاربری از قبل وجود دارد');
        header("Location: ../register.php");
    } else {
        if ($password != $passConf) {
            setMessage('رمز عبور و تکرار آن باهم برابر نیستند');
            header("Location: ../register.php");
        } else {
            $insert = mysqli_query($db, "INSERT INTO users (display_name, username,password) VALUES ('$displayname', '$username','$password')");
            mysqli_query($db, 'SET NAMES utf8');

            if ($insert) {
                setMessage('ثبت نام با موفقیت انجام شد. اکنون می توانید وارد شوید');
                header("Location: ../login.php");
            } else {
                echo 'error';
            }
        }
    }
}
// check login
if (isset($_POST['do-login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $checkUser = mysqli_query($db, "SELECT * FROM users WHERE username='$username' AND password = '$password'");
    if (mysqli_num_rows($checkUser) > 0) {
        $_SESSION['loggedin'] = $username;
        header('Location: ../index.php');
    } else {
        setMessage('نام کاربری یا کلمه ی عبور اشتباه است');
        header("Location: ../login.php");
    }
}

function checkLogin()
{
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
    }
}
// do logout
if (isset($_GET['logout'])) {
    unset($_SESSION['loggedin']);
    header("Location: index.php");
}
// add note
if (isset($_POST['user-note'])) {
    $userNote = $_POST['user-note'];
    $userId = getUserId();

    $addNote = mysqli_query($db, "INSERT INTO notes (note_text, user_id) VALUES ('$userNote', '$userId')");

    if ($addNote) {
        header("Location: ../index.php");
    }
};

// get user notes
function getUserNotes($limit = false)
{
    global $db;
    $userId = getUserId();
    if ($limit) {
        $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id = $userId AND is_done='0' ORDER BY id DESC LIMIT $limit");
    } elseif (!$limit) {
        $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id = $userId AND is_done='0' ORDER BY id DESC");
    }
    $userNotes = [];
    while ($notes = mysqli_fetch_array($getNotes)) {
        $userNotes[] = $notes;
    }
    return $userNotes;
}


// get user id from username
function getUserId()
{
    global $db;
    $username = $_SESSION['loggedin'];
    $getUser = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");
    $userArray = mysqli_fetch_array($getUser);
    return $userArray['id'];
    
}
// get user display name

function getUserDisplayName(){
    global $db;
    $username = $_SESSION['loggedin'];
    $getUser = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");
    $userArray = mysqli_fetch_array($getUser);
    return $userArray['display_name'];
    
}
// check done or not
if(isset($_GET['done'])){
    $noteId = $_GET['done'];
    $updateNote = mysqli_query($db, "UPDATE notes SET is_done='1' WHERE id = '$noteId'");
    if($updateNote){
        header('Location: notes.php');
    }
}
// get done notes
function getDoneUserNotes()
{
    global $db;
    $userId = getUserId();
     
        $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id = $userId AND is_done='1' ORDER BY id DESC");
    
    $userNotes = [];
    while ($notes = mysqli_fetch_array($getNotes)) {
        $userNotes[] = $notes;
    }
    return $userNotes;
}
// delete notes

if(isset($_GET['delete'])) {
    $noteId = $_GET['delete'];
    $deleteNote = mysqli_query($db, "DELETE FROM notes WHERE id = '$noteId'");
    if($deleteNote){
        header('Location: notes.php');
    }
}
// search

if(isset($_GET['search'])){
    function getSearchResult(){
    global $db;
    $searchInput = $_GET['search'];
    $userId = getUserId();
    $search = mysqli_query($db,"SELECT * FROM notes WHERE note_text LIKE '%$searchInput%' AND user_id=$userId AND is_done=0");
    
    $searchResults = [];
    while($result = mysqli_fetch_array($search)){
        $searchResults[] = $result;
    }
    return $searchResults;
}
}

// Get user data for setting page
function getUserData(){
    global $db;
    $userId = getUserId();
    $getUsername = mysqli_query($db, "SELECT * FROM users WHERE id = '$userId'");
    $userData = mysqli_fetch_array($getUsername);
    return $userData;
}
// update user data

if(isset($_POST['do-update'])){
    $newDisplayName = $_POST['display-name'];
    $newTitle = $_POST['title'];
    $newSubitle = $_POST['subtitle'];
    $userId = getUserId();
    $updateSetting = mysqli_query($db, "UPDATE users SET display_name = '$newDisplayName' , title = 
    '$newTitle' , subtitle = '$newSubitle' WHERE id='$userId'");
    if($updateSetting) {
        setMessage('اطلاعات با موفقیت تغییر یافت.');
        header("Location: ../setting.php");
    }
}
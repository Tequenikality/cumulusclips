<?php

### Created on March 15, 2009
### Created by Miguel A. Hurtado
### This script performs all the user actions for a video via AJAX


// Include required files
include ('../config/bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Video');
App::LoadClass ('Favorite');


// Establish page variables, objects, arrays, etc
$logged_in = User::LoginCheck();
if ($logged_in) $user = new User ($logged_in);



// Verify a valid video was provided
if (empty ($_POST['video_id']) || !is_numeric ($_POST['video_id']))  App::Throw404();
$video = new Video ($_POST['video_id']);
if (!$video->found || $video->status != 6) App::Throw404();


// Verify user is logged in
if (!$logged_in) {
    echo json_encode (array ('result' => 0, 'msg' => (string) Language::GetText('error_favorite_login')));
    exit();
}


// Check user doesn't fav. his own video
if ($user->user_id == $video->user_id) {
    echo json_encode (array ('result' => 0, 'msg' => (string) Language::GetText('error_favorite_own')));
    exit();
}


// Create Favorite record if none exists
$data = array ('user_id' => $user->user_id, 'video_id' => $video->video_id);
if (!Favorite::Exist ($data)) {
    Favorite::Create ($data);
    echo json_encode (array ('result' => 1, 'msg' => (string) Language::GetText('success_favorite_added')));
    exit();
} else {
    echo json_encode (array ('result' => 0, 'msg' => (string) Language::GetText('error_favorite_duplicate')));
    exit();
}

?>
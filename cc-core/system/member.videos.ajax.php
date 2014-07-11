<?php

$this->view->disableView = true;
$userMapper = new UserMapper();
$videoMapper = new VideoMapper();
$limit = 8;
$start = 9;

// Verify a user was selected
if (!empty($_GET['userId'])) {
    $user = $userMapper->getUserById($_GET['userId']);
} else {
    App::Throw404();
}

// Check if user is valid
if (!$user || $user->status != 'approved') {
    App::Throw404();
}

// Validate video limit
if (!empty($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) {
    $limit = $_GET['limit'];
}

// Validate starting record
if (!empty($_GET['start']) && is_numeric($_GET['start'])) {
    $start = $_GET['start'];
}

// Retrieve video list
$db = Registry::get('db');
$query = "SELECT video_id FROM " . DB_PREFIX . "videos WHERE status = 'approved' and user_id = :userId ORDER BY date_created DESC LIMIT :start, $limit";
$videoResults = $db->fetchAll($query, array(
    ':userId' => $user->userId,
    ':start' => $start
));

$videoList = $videoMapper->getVideosFromList(Functions::arrayColumn($videoResults, 'video_id'));
echo json_encode($videoList);
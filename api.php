<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Access-Control-Allow-Origin: ytscoop.com");


if (isset($_GET['url'])) {
  # Fetch stream attributes, stream locations, and cipher JavaScript.
  $url = $_GET['url'];
  if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
    $id = extractID($url);
    # Fetch necessary information
    $info = getVideoInfo($id); # raw video info data
    $watch = getWatchHTML($id); # raw html from the video watch page
    $jsname = getJSName($watch); # the location of the JavaScript file

    $info_json = extractResponseJSON(urldecode($info));
    echo $info_json;

    # Send back:
    # - the video title
    # - for each of the following streams:
    #     + the highest-resolution video stream
    #     + the highest-bitrate audio stream
    #     + the highest-resolution progressive stream
    # - include:
    #     + the deciphered stream URL
    #     + the dimensions and fps, for video
    #     + the bitrate, for audio
  }
}


function extractID($url) {
  preg_match_all("~(?:v=|\/)([0-9A-Za-z_-]{11}).*~",
    $url, $out, PREG_PATTERN_ORDER);
  return $out[1][0];
}

function getWatchHTML($id) {
  return file_get_contents('https://youtube.com/watch?v=' . $id);
}

function getVideoInfo($id) {
  $url = "https://youtube.com/get_video_info?ps=default&hl=en_US"
    . "video_id=" . $id
    . "&eurl=https%3A//www.youtube.com/watch%3Fv%3D" . $id;
  return file_get_contents($url);
}

function getJSName($html) {
  preg_match_all("~(/s/player/[\w\d]+/[\w\d_/.]+/base\.js)~",
    $html, $out, PREG_PATTERN_ORDER);
  return $out[0][1];
}

function extractResponseJSON($raw) {
  $url_pairs = explode('&', $raw);
  foreach($url_pairs as $url_pair) {
    $div = strpos($url_pair, '=');
    $key = substr($url_pair, 0, $div);
    if ($key == "player_response") {
      return substr($url_pair, $div + 1);
    }
  }
}


?>

<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Access-Control-Allow-Origin: ytscoop.com");

function urlToID($url) {
  preg_match_all("/(?:v=|\/)([0-9A-Za-z_-]{11}).*/",
    $url, $out, PREG_PATTERN_ORDER);
  return $out[1][0];
}

function htmlToJavascript($html) {
  preg_match_all("/(/s/player/[\w\d]+/[\w\d_/.]+/base\.js)/",
    $html, $out, PREG_PATTERN_ORDER);
  return $out[0][1];
}

function htmlToConfig($html) {

}

if (isset($_GET['url'])) {
  # Fetch stream attributes, stream locations, and cipher JavaScript.
  $url = $_GET['url'];
  if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
    $id = urlToID($url);
    echo $id;
    $html = file_get_contents('https://youtube.com/watch?v=' . $id);
    $jsname = htmlToJavascript($html);
    echo $jsname;
  }

  echo "URL: " . $_GET['url'];

} else if (isset($_GET['js']) && isset($_GET['choices'])) {
  # Decipher selected stream URLs and return them.
  # Choices should be a semicolon/comma-separated string:
  #  - individual streams are separated by semicolons
  #  - within each stream 'block', the stream type, quality descriptor, and ciphered URL are separated by commas
  #    e.g. adaptive,1080p at 30fps and 192kbps,https://...
  echo "getting cipher and handling choices";

}

?>

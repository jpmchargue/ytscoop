<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Access-Control-Allow-Origin: ytscoop.com");

if (isset($_GET['url'])) {
  # Fetch stream attributes, stream locations, and cipher JavaScript.
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

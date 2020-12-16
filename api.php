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
    $info_json = extractResponseJSON(urldecode($info));

    # Encrypted URL format testing
    #echo var_dump($info_json->streamingData->formats) . '</br>';

    $num_pro = count($info_json->streamingData->formats);
    echo strval($num_pro) . " progressive streams found<br>";
    $best_pro = NULL;
    foreach($info_json->streamingData->formats as $s) {
      if ($best_pro == NULL || $s->width > $best_pro->width) {
        $best_pro = $s;
      }
    }

    $num_adapt = count($info_json->streamingData->adaptiveFormats);
    echo strval($num_adapt) . " adaptive streams found<br>";
    $best_video = NULL;
    $best_audio = NULL;
    foreach($info_json->streamingData->adaptiveFormats as $s) {
      $type = substr($s->mimeType, 0, 5);
      if ($type == "video") {
        if ($best_video == NULL || $s->width > $best_video->width) {
          $best_video = $s;
        } else if ($s->width == $best_video->width && $s->fps > $best_video->fps) {
          $best_video = $s;
        }
      }
      else if ($type == "audio") {
        if ($best_audio == NULL || $s->bitrate > $best_audio->bitrate) {
          $best_audio = $s;
        }
      }
    }

    $quality_map = array(
      "AUDIO_QUALITY_LOW"=>"low",
      "AUDIO_QUALITY_MEDIUM"=>"medium",
      "AUDIO_QUALITY_HIGH"=>"high",
    );

    if (property_exists($best_pro, 'url')
      && (strpos($best_pro->url, "signature=") !== false
      || (strpos($best_pro->url, "sig=") !== false && strpos($best_pro->url, '&s=') === false))) {
      $response = array(
        array(
          "desc"=>$best_pro->qualityLabel." @ ".$best_pro->fps." fps with ".$quality_map[$best_pro->audioQuality]." audio quality",
          "url"=>$best_pro->url
        ),
        array(
          "desc"=>$best_video->qualityLabel . " @ " . $best_video->fps . " fps",
          "url"=>$best_video->url
        ),
        array(
          "desc"=>strval(round(floatval($best_pro->bitrate)/8192)) . " kbps (" . $quality_map[$best_audio->audioQuality] . ")",
          "url"=>$best_audio->url
        )
      );
      echo json_encode($response);
      #echo '<br>';
      #echo "Best Progressive Stream: " . $best_pro->qualityLabel . " @ "
      #  . $best_pro->fps . "fps with " . $quality_map[$best_pro->audioQuality] . " audio quality"
      #  . ": <br>" . audioTag($best_pro->url) . "<br>";
      #echo "Best Video Stream: " . $best_video->qualityLabel . " @ " . $best_video->fps . " fps"
      #  . ": <br>" . audioTag($best_video->url) . "<br>";
      #echo "Best Audio Stream: " . strval(round(floatval($best_audio->bitrate)/8192)) . " kbps (" . $quality_map[$best_audio->audioQuality] . ")"
      #  . ": <br>" . audioTag($best_audio->url) . "<br>";

    } else {
      # The stream URLs must be decrypted.
      # The decryption algorithm can be reverse-engineered via the video's JavaScript file;
      # the location of this file can be found in the video's watch page HTML.
      echo "The URLs of this video's streams are <i>encrypted</i>.";

      # Get the JavaScript file
      $watch = getWatchHTML($id);
      $jsname = getJSName($watch);
      $js = file_get_contents('https://youtube.com' . $jsname);

      # Find location of and parse cipher function
      $cipher = getCipher($js);

      # Find code and URL, then apply the cipher
      $best_pro_url = decipherURL($best_pro->signatureCipher, $cipher);
      $best_video_url = decipherURL($best_video->signatureCipher, $cipher);
      $best_audio_url = decipherURL($best_audio->signatureCipher, $cipher);
      $response = array(
        array(
          "desc"=>$best_pro->qualityLabel." @ ".$best_pro->fps." fps with ".$quality_map[$best_pro->audioQuality]." audio quality",
          "url"=>$best_pro_url
        ),
        array(
          "desc"=>$best_video->qualityLabel . " @ " . $best_video->fps . " fps",
          "url"=>$best_video_url
        ),
        array(
          "desc"=>strval(round(floatval($best_pro->bitrate)/8192)) . " kbps (" . $quality_map[$best_audio->audioQuality] . ")",
          "url"=>$best_audio_url
        )
      );
      echo json_encode($response);
    }


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

    # Add means of measuring time from request to response, for testing
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
    . "&video_id=" . $id
    . "&eurl=https%3A//www.youtube.com/watch%3Fv%3D" . $id;
  return file_get_contents($url);
}

function getJSName($html) {
  preg_match_all("~(/s/player/[\w\d]+/[\w\d_/.]+/base\.js)~",
    $html, $out, PREG_PATTERN_ORDER);
  return $out[0][1];
}

function extractResponseJSON($raw) {
  # Returns an object representing the metadata JSON in an info file.
  $url_pairs = explode('&', $raw);
  foreach($url_pairs as $url_pair) {
    $div = strpos($url_pair, '=');
    $key = substr($url_pair, 0, $div);
    if ($key == "player_response") {
      return json_decode(substr($url_pair, $div + 1));
    }
  }
}

function audioTag($url) {
  # Creates an HTML audio tag with the given URL as a source.
  return '<audio src="' . $url . '" controls></audio>';
}

function getCipher($js) {
  # Returns a array of 2-element array.
  # Each interior array represents a transformation in the signature cipher as follows:
  # [0] - the name of the PHP function representing the transformation (e.g. cipher_swap)
  # [1] - the number used as the second argument in the transformation
  $transform_function_name = getCipherFunctionName($js);
  $transform_list = getTransformList($js, $transform_function_name);
  $action_class = getActionClass($js, explode('.', $transform_list[0])[0]);
  $js_to_php = getFunctionMapping($action_class);
  $cipher = [];
  foreach($transform_list as $t) {
    $parts = preg_split('~[,)(\.]+~', $t);
    $cipher[] = array($js_to_php[$parts[1]], intval($parts[3]));
  }
  return $cipher;
}

function decipherURL($sig, $cipher) {
  $pairs = explode('&', $sig);
  $code = explode('=', $pairs[0])[1];
  $raw_url = urldecode(explode('=', end($pairs))[1]);
  foreach($cipher as $step) {
    $code = $step[0]($code, $step[1];
  }
  return $raw_url . '&sig=' . $code;
}

function getCipherFunctionName($js) {
  $patterns = array(
        "~\b[cs]\s*&&\s*[adf]\.set\([^,]+\s*,\s*encodeURIComponent\s*\(\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\b[a-zA-Z0-9]+\s*&&\s*[a-zA-Z0-9]+\.set\([^,]+\s*,\s*encodeURIComponent\s*\(\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        '~(?:\b|[^a-zA-Z0-9$])(?P<sig>[a-zA-Z0-9$]{2})\s*=\s*function\(\s*a\s*\)\s*{\s*a\s*=\s*a\.split\(\s*""\s*\)~',
        '~(?P<sig>[a-zA-Z0-9$]+)\s*=\s*function\(\s*a\s*\)\s*{\s*a\s*=\s*a\.split\(\s*""\s*\)~',
        '~(["\'])signature\1\s*,\s*(?P<sig>[a-zA-Z0-9$]+)\(~',
        "~\.sig\|\|(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~yt\.akamaized\.net/\)\s*\|\|\s*.*?\s*[cs]\s*&&\s*[adf]\.set\([^,]+\s*,\s*(?:encodeURIComponent\s*\()?\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\b[cs]\s*&&\s*[adf]\.set\([^,]+\s*,\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\b[a-zA-Z0-9]+\s*&&\s*[a-zA-Z0-9]+\.set\([^,]+\s*,\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\bc\s*&&\s*a\.set\([^,]+\s*,\s*\([^)]*\)\s*\(\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\bc\s*&&\s*[a-zA-Z0-9]+\.set\([^,]+\s*,\s*\([^)]*\)\s*\(\s*(?P<sig>[a-zA-Z0-9$]+)\(~",
        "~\bc\s*&&\s*[a-zA-Z0-9]+\.set\([^,]+\s*,\s*\([^)]*\)\s*\(\s*(?P<sig>[a-zA-Z0-9$]+)\(~"
    );
    foreach($patterns as $p) {
      if (preg_match_all($p, $js, $out, PREG_PATTERN_ORDER)) {
        return $out[1][0];
      }
    }
    return "";
}

function getTransformList($js, $func) {
  preg_match_all("~".$func."=function\(\w\){[a-z=\.\(\"\)]*;(.*);(?:.+)}~",
    $js, $out, PREG_PATTERN_ORDER);
  return explode(';', $out[1][0]);
}

function getActionClass($js, $class) {
  preg_match_all("~var ".$class."={(.*?)};~s", $js, $out, PREG_PATTERN_ORDER);
  $match = str_replace('\n', ' ', $out[1][0]);
  $returned = explode('},', $match);
  return $returned;
}

function getFunctionMapping($action_class) {
  $map = [];
  foreach($action_class as $a) {
    $func_name = str_replace(' ', '', explode(':', $a)[0]);
    if (strpos($a, "reverse") !== false) {
      $map[$func_name] = 'cipher_reverse';
    } else if (strpos($a, "splice") !== false) {
      $map[$func_name] = 'cipher_splice';
    } else {
      $map[$func_name] = 'cipher_swap';
    }
  }
  return $map;
}

function cipher_reverse($str, $n) {
  return strrev($str, $n);
}

function cipher_splice($str, $n) {
  return substr($str, $n);
}

function cipher_swap($str, $n) {
  $index = $n % strlen($str);
  if ($index == 0) return $str;
  $acc = substr($str, $index, 1)
    . substr($str, 1, $index - 1)
    . substr($str, 0, 1)
    . substr($str, $index + 1);
  return $acc;
}

?>

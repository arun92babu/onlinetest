<?php

// Database connection (optional)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function base64url_encode($data) {
  $b64 = base64_encode($data);
  if ($b64 === false) {
    return false;
  }
  $url = strtr($b64, '+/', '-_');
  return rtrim($url, '=');
}

$token  = "Bmn0c8rQDJoGTibk";                 // base64_encode(random_bytes(12));
$secret = "yXWczx0LwgKInpMFfgh0gCYCA8EKbOnw"; // base64_encode(random_bytes(24));

// RFC-defined structure
$header = [
    "alg" => "HS256",
    "typ" => "JWT"
];

// whatever you want
$payload = [
    "token" => $token,
    "stamp" => "2020-01-02T22:00:00+00:00"    // date("c")
];

$jwt = sprintf(
    "%s.%s",
    base64url_encode(json_encode($header)),
    base64url_encode(json_encode($payload))
);

$jwt = sprintf(
    "%s.%s",
    $jwt,
    base64url_encode(hash_hmac('SHA256', $jwt, base64_decode($secret), true))
);



?>
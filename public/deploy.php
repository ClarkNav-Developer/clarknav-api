<?php

$secret = trim(file_get_contents("/var/www/clarknav-api/github_secret")); 
$signature = "sha256=" . hash_hmac("sha256", file_get_contents("php://input"), $secret);

if (!hash_equals($signature, $_SERVER["HTTP_X_HUB_SIGNATURE_256"])) {
    http_response_code(403);
    die("Invalid signature");
}

exec("sh /var/www/clarknav/deploy.sh > /var/www/clarknav-api/deploy.log 2>&1 &");

http_response_code(200);
echo "Deployment triggered!";


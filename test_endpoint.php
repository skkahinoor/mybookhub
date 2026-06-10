<?php
$url = 'http://localhost/mybookhub/public/bookgenie-search?q=' . urlencode('CBSE Class 10 Math');
echo "Fetching: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Response:\n";
echo $response;
echo "\n";

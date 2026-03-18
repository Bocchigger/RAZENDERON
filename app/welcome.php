<?php

$pageTitle = 'Welcome - Razenderon';
include __DIR__ . '/_header.php';


if (loggedIn()) {
    $welcome_message = "";

} else {

    $welcome_message = "<p>Please <a href='/login'>login</a> or <a href='/signup'>create an account</a> to access the dashboard or view the public <a href='/browse'>asset list</a>.</p>";
}


$html = file_get_contents(__DIR__.'/view/welcome.html');
$html = str_replace('{{welcome_message}}', $welcome_message, $html);

echo $html;

include __DIR__ . '/_footer.php';
<?php

// please install
// composer require deeplcom/deepl
// please write deepl-api-key.php
// <?php
// $authKey='{your auth key}';

require "build/deepl-api-key.php";

if ($authKey) {
 //   echo "Auth Key: $authKey";
} else {
    echo "Auth Key not set";
	exit;
}

// $argv[0] はスクリプト名
array_shift($argv);

$args = [];
foreach ($argv as $arg) {
    if (preg_match('/^--([^=]+)=(.*)$/', $arg, $match)) {
        $args[$match[1]] = $match[2];
    } elseif (preg_match('/^--([^ ]+)$/', $arg, $match)) {
        $args[$match[1]] = true;
    }
}

if (isset($args['input'])) {
    $input=$args['input'];
}

if (isset($args['to'])) {
    $to= $args['to'];
}

if (isset($args['from'])) {
    $from= $args['from'];
}
//$input="こんにちわ世界";
//$from="JA";
//$to="en-US";
//exit;

if($to == "EN") {
	$to = "EN-US";
}
if($to == "PT") {
	$to = "PT-BR";
}

require 'vendor/autoload.php';

use DeepL\Translator;
$translator = new \DeepL\Translator($authKey);

$result = $translator->translateText($input, $from, $to);
//$result = $translator->translateText('ハローワールド!', null, 'EN-US');
echo $result->text . "\n"; // Bonjour, le monde!

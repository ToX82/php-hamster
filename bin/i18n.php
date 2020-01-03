<?php
include __DIR__ . "/../config.php";
include __DIR__ . "/../libs/libs.php";

$logics = glob(__DIR__ . '/../logics/*.php');
$templates = glob(__DIR__ . '/../templates/*/*.php');

$strings = [];
$strings = array_merge($strings, iterate($logics));
$strings = array_merge($strings, iterate($templates));

$languages = glob(__DIR__ . '/../i18n/*.json');
update($languages, $strings);
echo "All done!";

function iterate($files)
{
    $strings = [];

    foreach ($files as $file) {
        $lines = file($file);
        foreach ($lines as $line) {
            if (preg_match('/__\(.*\)/', $line, $matches)) {
                $string = substr($matches[0], 4, -2);
                $strings[$string] = '';
            }
        }
    }

    return $strings;
}

function update($languages, $strings)
{
    foreach ($languages as $language) {
        $original = file_get_contents($language);
        $original = json_decode($original, true);

        foreach ($strings as $key => $string) {
            if (!isset($original[$key])) {
                $original[$key] = '';
            }
        }
        file_put_contents($language, json_encode($original, JSON_PRETTY_PRINT));
    }
}

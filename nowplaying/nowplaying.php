<?php
/**
 * nowplaying.php - Forge Radio Myriad OCP -> Website connector
 * Params:
 *      artist
 *      album
 *      title
 *      showname
 */

 define("NOWPLAYINGPATH", "./nowplaying.json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // WRITE NOW PLAYING
    $artist = $_POST['artist'] or die('No artist specified');
    $album = $_POST['album'] or die('No album specified');
    $title = $_POST['title'] or die('No title specified');
    $showname = $_POST['showname'] or 'Forge Radio';

    $payload = array(
        'artist' => $artist,
        'album' => $album,
        'title' => $title,
        'showname' => $showname,
    )

    $file = fopen(NOWPLAYINGPATH, "w"); 
    fwrite($file , json_encode($payload)); 
    fclose($file);

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // RETURN NOW PLAYING
    readfile(NOWPLAYINGPATH) or die(json_encode(array()));
}

?>
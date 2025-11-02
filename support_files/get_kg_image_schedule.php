<?php
$filename = "../../files/ejamaat_photos/".$kg_its.".JPG";
if (file_exists($filename)) {
    $filename = "https://colombojamaat.org/files/ejamaat_photos/".$kg_its.".JPG";
} else {
    $filename = "../../files/ejamaat_photos/".$kg_its.".jpg";
    if (file_exists($filename)) {
        $filename = "https://colombojamaat.org/files/ejamaat_photos/".$kg_its.".jpg";
    } else {
        $filename = "https://m.colomboshabab.com/images/no_image.jpg";
    }
}
?>
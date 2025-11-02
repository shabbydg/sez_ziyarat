<?php
$filename = "../../../anjuman/files/ejamaat_photos/".$kg_its.".JPG";
if (file_exists($filename)) {
    $filename = "http://colombojamaat.org/files/ejamaat_photos/".$kg_its.".JPG";
} else {
    $filename = "../../../anjuman/files/ejamaat_photos/".$kg_its.".jpg";
    if (file_exists($filename)) {
        $filename = "http://colombojamaat.org/files/ejamaat_photos/".$kg_its.".jpg";
    } else {
        $filename = "../../../images/no_image.jpg";
    }
}
?>
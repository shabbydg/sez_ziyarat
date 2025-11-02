<?php
//$filename = "../../../anjuman/files/ejamaat_photos/".$kun_its.".JPG";
//if (file_exists($filename)) {
//    $filename = "https://colombojamaat.org/files/ejamaat_photos/".$kun_its.".JPG";
//} else {
//    $filename = "../../../anjuman/files/ejamaat_photos/".$kun_its.".jpg";
//    if (file_exists($filename)) {
//        $filename = "https://colombojamaat.org/files/ejamaat_photos/".$kun_its.".jpg";
//    } else {
//        $filename = "https://m.colomboshabab.com/images/no_image.jpg";
//    }
//}

$filename = "https://colombojamaat.org/files/ejamaat_photos/".$kun_its.".jpg";
// echo $filename;

$file_headers = @get_headers($filename);
if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
    echo "https://m.colomboshabab.com/images/no_image.jpg";
}
else {
    echo $filename;
}
?>
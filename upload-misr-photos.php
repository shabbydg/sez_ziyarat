<?php
require_once 'common/config.php';
require_once 'common/functions.php';

$safarid = (isset($_POST['safarid']) && $_POST['safarid'] != '') ? $_POST['safarid'] : '0';
$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? $_POST['kunregid'] : '0';

$user_id = getActiveUser();

//let's upload the files
$file_count = count($_FILES['kunPhotos']['name']);

if($file_count > 0){
    for($i=0; $i<$file_count; $i++) {
        if(trim($_FILES['kunPhotos']['tmp_name'][$i]) == '') continue;
        $files = uploadFile($i, SRV_ROOT . 'images/uploads/');

        // $mainImage = $images['image'];
//         $thumbnail = $images['thumbnail'];
//         $thumbnailMid = $images['thumbnailMid'];

		$mainFile = $files['file'];

        $sql_image = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_upload_time, photo_user_id) VALUES ($kunregid, '$mainFile', '', NOW(), $user_id)";
        dbQuery($sql_image);
    }
}

header("Location: " . $server_link . "safars/kun-safar/$safarid");

function uploadImageFile($count, $uploadDir)
{
    //$image     = $_FILES['fleImage'];
    $imagePath = '';
    $thumbnailPath = '';
    $thumbnailMidPath =  '';

    // if a file is given
    if (trim($_FILES['kunPhotos']['tmp_name'][$count]) != '') {
        //if(trim($_FILES['kunPhotos']['tmp_name'][$count]) == '') continue;
        $ext = substr(strrchr($_FILES['kunPhotos']['name'][$count], "."), 1);

        // generate a random new file name to avoid name conflict
        $imagePath = md5(rand() * time()) . ".$ext";

        list($width, $height, $type, $attr) = getimagesize($_FILES['kunPhotos']['tmp_name'][$count]);

        // make sure the image width does not exceed the
        // maximum allowed width
        if (LIMIT_PRODUCT_WIDTH && $width > MAX_PRODUCT_IMAGE_WIDTH) {
            $result    = createThumbnail($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $imagePath, MAX_PRODUCT_IMAGE_WIDTH);
            $imagePath = $result;
        } else {
            $result = move_uploaded_file($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $imagePath);
        }

        if ($result) {
            // create thumbnail
            $thumbnailPath =  $imagePath . ".$ext";
            $thumbnailMidPath =  md5(rand() * time()) . ".$ext";
            $result = createThumbnail($uploadDir . $imagePath, $uploadDir . $thumbnailPath, MAX_PRODUCT_IMAGE_WIDTH);
            $resultMid = createThumbnail($uploadDir . $imagePath, $uploadDir . $thumbnailMidPath, MAX_PRODUCT_IMAGE_WIDTH); //width for mid image is always 220

            // create thumbnail failed, delete the image
            if (!$result) {
                unlink($uploadDir . $imagePath);
                $imagePath = $thumbnailPath = $thumnailMidPath = '';
            } else {
                $thumbnailPath = $result;
            }

            // create thumbnailmid failed, delete the image
            if (!$resultMid) {
                unlink($uploadDir . $imagePath);
                $imagePath = $thumbnailPath = $thumbnailMidPath = '';
            } else {
                $thumbnailMidPath = $resultMid;
            }
        } else {
            // the image cannot be upload / resized
            $imagePath = $thumbnailPath = $thumbnailMidPath = '';
        }

    }
    return array('image' => $imagePath, 'thumbnail' => $thumbnailPath, 'thumbnailMid' => $thumbnailMidPath);
}


function uploadFile($count, $uploadDir)
{
    //$image     = $_FILES['fleImage'];
    $imagePath = '';
    $thumbnailPath = '';
    $thumbnailMidPath =  '';

    // if a file is given
    if (trim($_FILES['kunPhotos']['tmp_name'][$count]) != '') {
        $ext = substr(strrchr($_FILES['kunPhotos']['name'][$count], "."), 1);
        // generate a random new file name to avoid name conflict
        $filePath = md5(rand() * time()) . ".$ext";
    
        //$filePath = $_FILES['kunPhotos']['name'][$count];

        $result = move_uploaded_file($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $filePath);

        if (!$result) {
            // the image cannot be upload / resized
            $filePath = '';
        }
    }
    return array('file' => $filePath);
}
?>
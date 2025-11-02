<?php
require_once 'common/config.php';
require_once 'common/functions.php';
require '../PHPMailer/PHPMailerAutoload.php';

$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? $_POST['kunregid'] : 0; //0 means it's a new registration

//$user_id = getActiveUser();

$safarid = $_POST['safarid'];
$its = $_POST['its'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$fname = $_POST['fname'];
$mname = $_POST['mname'];
$lname = $_POST['lname'];
$ppno = $_POST['ppno'];
$nationality = $_POST['nationality'];
$ppissue = $_POST['ppissue'];
$ppexpiry = $_POST['ppexpiry'];
$placeofissue = $_POST['placeofissue'];
$countryofissue = $_POST['countryofissue'];
$dob = $_POST['dob'];
$birthplace = $_POST['birthplace'];
$gender = $_POST['gender'];
$orignationality = $_POST['orignationality'];
$birthcountry = $_POST['birthcountry'];
$curroccupation = $_POST['curroccupation'];
$address = $_POST['address'];
$langspoken = $_POST['langspoken'];

// New fields from modernized form
$age = isset($_POST['age']) ? $_POST['age'] : '';
$passport_arabic_name = isset($_POST['passport_arabic_name']) ? $_POST['passport_arabic_name'] : '';
$father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
$father_name_arabic = isset($_POST['father_name_arabic']) ? $_POST['father_name_arabic'] : '';
$grandfather_name = isset($_POST['grandfather_name']) ? $_POST['grandfather_name'] : '';
$grandfather_name_arabic = isset($_POST['grandfather_name_arabic']) ? $_POST['grandfather_name_arabic'] : '';
$mother_name = isset($_POST['mother_name']) ? $_POST['mother_name'] : '';
$mother_name_arabic = isset($_POST['mother_name_arabic']) ? $_POST['mother_name_arabic'] : '';
$mother_father_name = isset($_POST['mother_father_name']) ? $_POST['mother_father_name'] : '';
$mother_father_name_arabic = isset($_POST['mother_father_name_arabic']) ? $_POST['mother_father_name_arabic'] : '';
$passport_type = isset($_POST['passport_type']) ? $_POST['passport_type'] : '';
$professional = isset($_POST['professional']) ? $_POST['professional'] : '';

// Set default for accommodation (removed from form)
$accommodation_required = 'Yes';
$spname = $_POST['spname'];
$spoccupation = $_POST['spoccupation'];
$spnationality = $_POST['spnationality'];
$spdob = $_POST['spdob'];
$sppob = $_POST['sppob'];
$accomppersons = $_POST['accomppersons'];
$profession = $_POST['profession'];
$certs = $_POST['certs'];
$issuecerts = $_POST['issuecerts'];
$dateissuecerts = $_POST['dateissuecerts'];
$numpreventry = $_POST['numpreventry'];
$datelentry = $_POST['datelentry'];
$child1name = $_POST['child1name'];
$child1dob = $_POST['child1dob'];
$child1occ = $_POST['child1occ'];
$child2name = $_POST['child2name'];
$child2dob = $_POST['child2dob'];
$child2occ = $_POST['child2occ'];
$child3name = $_POST['child3name'];
$child3dob = $_POST['child3dob'];
$child3occ = $_POST['child3occ'];
$visit1country = $_POST['visit1country'];
$visit1reason = $_POST['visit1reason'];
$visit1date = $_POST['visit1date'];
$visit2country = $_POST['visit2country'];
$visit2reason = $_POST['visit2reason'];
$visit2date = $_POST['visit2date'];
$visit3country = $_POST['visit3country'];
$visit3reason = $_POST['visit3reason'];
$visit3date = $_POST['visit3date'];


if($kunregid == 0){
    //INSERT
    $sql_insert = "INSERT INTO `zyt_kun_registrations` (`safar_id`, `kun_its`, `kun_fname`, `kun_mname`, `kun_lname`, `kun_ppno`, `kun_nationality`, `kun_ppissue`, `kun_ppexpiry`, `kun_placeofissue`, `kun_dob`, `kun_birthplace`, `kun_gender`, `kun_orignationality`, `kun_birthcountry`, `kun_curroccupation`, `kun_address`, `kun_langspoken`, `kun_spname`, `kun_spoccupation`, `kun_spdob`, `kun_sppob`, `kun_accomppersons`, `kun_profession`, `kun_certs`, `kun_issuecerts`, `kun_dateissuecerts`, `kun_numpreventry`, `kun_datelentry`, `kun_child1name`, `kun_child1dob`, `kun_child1occ`, `kun_child2name`, `kun_child2dob`, `kun_child2occ`, `kun_child3name`, `kun_child3dob`, `kun_child3occ`, `kun_visit1country`, `kun_visit1reason`, `kun_visit1date`, `kun_visit2country`, `kun_visit2reason`, `kun_visit2date`, `kun_visit3country`, `kun_visit3reason`, `kun_visit3date`, `kun_status`, `kun_contact`, `kun_email`, `kun_declaration`, `kun_spnationality`, `kun_countryofissue`,`kun_lastupdate`,`kun_lastupdatememid`) VALUES ('$safarid', '$its', '$fname', '$mname', '$lname', '$ppno', '$nationality', '$ppissue', '$ppexpiry', '$placeofissue', '$dob', '$birthplace', '$gender', '$orignationality', '$birthcountry', '$curroccupation', '$address', '$langspoken', '$spname', '$spoccupation', '$spdob', '$sppob', '$accomppersons', '$profession', '$certs', '$issuecerts', '$dateissuecerts', '$numpreventry', '$datelentry', '$child1name', '$child1dob', '$child1occ', '$child2name', '$child2dob', '$child2occ', '$child3name', '$child3dob', '$child3occ', '$visit1country', '$visit1reason', '$visit1date', '$visit2country', '$visit2reason', '$visit2date', '$visit3country', '$visit3reason', '$visit3date', 'registered', '$contact', '$email', 1, '$spnationality', '$countryofissue',NOW(),'$its');";
    dbQuery($sql_insert);
} else {
    $sql_update = "UPDATE zyt_kun_registrations SET safar_id = '$safarid', kun_its = '$its', kun_fname = '$fname', kun_mname = '$mname', kun_lname = '$lname', kun_ppno = '$ppno', kun_nationality = '$nationality', kun_ppissue = '$ppissue', kun_ppexpiry = '$ppexpiry', `kun_placeofissue` = '$placeofissue', `kun_countryofissue` = '$countryofissue', `kun_dob` = '$dob', `kun_birthplace` = '$birthplace', `kun_gender` = '$gender', `kun_orignationality` = '$orignationality', `kun_birthcountry` = '$birthcountry', `kun_curroccupation` = '$curroccupation', `kun_address` = '$address', `kun_langspoken` = '$langspoken', `kun_spname` = '$spname', `kun_spoccupation` = '$spoccupation', `kun_spdob` = '$spdob', `kun_sppob` = '$sppob', `kun_spnationality` = '$spnationality', `kun_accomppersons` = '$accomppersons', `kun_profession` = '$profession', `kun_certs` = '$certs', `kun_issuecerts` = '$issuecerts', `kun_dateissuecerts` = '$dateissuecerts', `kun_numpreventry` = '$numpreventry', `kun_datelentry` = '$datelentry', `kun_child1name` = '$child1name', `kun_child1dob` = '$child1dob', `kun_child1occ` = '$child1occ', `kun_child2name` = '$child2name', `kun_child2dob` = '$child2dob', `kun_child2occ` = '$child2occ', `kun_child3name` = '$child3name', `kun_child3dob` = '$child3dob', `kun_child3occ` = '$child3occ', `kun_visit1country` = '$visit1country', `kun_visit1reason` = '$visit1reason', `kun_visit1date` = '$visit1date', `kun_visit2country` = '$visit2country', `kun_visit2reason` = '$visit2reason', `kun_visit2date` = '$visit2date', `kun_visit3country` = '$visit3country', `kun_visit3reason` = '$visit3reason', `kun_visit3date` = '$visit3date', `kun_contact` = '$contact', `kun_email` = '$email', `kun_declaration` = 1, `kun_lastupdate` = NOW(),`kun_lastupdatememid` = '$its' WHERE kun_id = $kunregid";
    dbQuery($sql_update);
}

header("Location: " . $server_link . "safars/kun-safar/$safarid");
?>
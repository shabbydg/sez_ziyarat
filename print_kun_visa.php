<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

$kunid = (isset($_GET['kunid']) && $_GET['kunid'] != '') ? $_GET['kunid'] : '';
if($kunid == '') header("Location:" . $_SERVER['HTTP_REFERER']);

$sql_print_data = "SELECT * FROM zyt_kun_registrations WHERE kun_id = $kunid";
$result_print_data = dbQuery($sql_print_data);
if(dbNumRows($result_print_data) <= 0){
	header("Location:" . $_SERVER['HTTP_REFERER']);
}

extract(dbFetchassoc($result_print_data));

?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            body{
                font-family: "Arial Rounded MT Bold", "Helvetica Rounded", Arial, sans-serif;
                font-weight: bold;
                font-size: 11px;
                text-transform: uppercase;
            }
            @page {
              size: A4;
              margin: 0;
            }
            .page {
                position:relative;
                width: 210mm;
                height: 297mm;
            }
            .page img.bg{
                position: absolute;
                width: 100%;
                height: 100%;
                z-index: 5;
                top:0;
                left:0;
            }
            .page img.absdots{
                position: absolute;
                z-index: 5;
                width: 9px;
                height: 9px;
				display: none;
            }
            .btn{
                color: #fff !important;
                padding: 5px;
                background: #ff0000;
                margin-right: 5px;
                text-decoration: none;
                border-radius: 3px;
            }
            .page div{
                position: absolute;
                z-index: 1000;
                color: #000;
                height: 13px;
            }
            @media print {
                .page{
                    width: 210mm;
                    height: 297mm;
                    margin:0;
                    padding:0;
                }
                .noprint{
                    display: none;
                }
            }
        </style>
    </head>
    <body style="position:relative;">
        <div class="noprint">
            <a href="<?php echo $server_link . "safars/kun-safar/$safar_id/edit-registration/$kunid"; ?>" class="btn btn-warning">Edit Registration</a>
            <a href="#" class="btn docuprint">Print</a>
        </div>
    <div class="page">
            <img src="pdf/kun_template_print_blank_high1.jpg" class="bg" />
            <div style="top: 294px; left: 140px; width: 500px; text-align: center;" class="formtext"><?php echo $kun_fname . " " . $kun_mname . " " . $kun_lname; ?></div>
            <div style="top: 318px; left: 100px; width: 320px; text-align:center;" class="formtext">ISLAM</div>
            <img src="images/dot.png" class="absdots" style="top: 323px; left: 550px; <?php if($kun_gender == 'M') echo "display:block;"; ?>" />
            <img src="images/dot.png" class="absdots" style="top: 323px; left: 658px; <?php if($kun_gender == 'F') echo "display:block;"; ?>" />
            <div style="top: 343px; left: 150px; width: 130px; text-align:center;" class="formtext"><?php echo $kun_orignationality; ?></div>
            <div style="top: 343px; left: 535px; width: 140px; text-align:center;" class="formtext"><?php echo $kun_nationality; ?></div>
            <div style="top: 366px; left: 150px; width: 130px; text-align:center;" class="formtext"><?php echo $kun_birthcountry; ?></div>
            <?php
			$kun_dob = printdateblanker($kun_dob);
			?>
            <div style="top: 366px; left: 535px; width: 45px; text-align:center;" class="formtext"><?php echo $kun_dob[0]; ?></div>
            <div style="top: 366px; left: 593px; width: 35px; text-align:center;" class="formtext"><?php echo $kun_dob[1]; ?></div>
            <div style="top: 366px; left: 640px; width: 35px; text-align:center;" class="formtext"><?php echo $kun_dob[2]; ?></div>
            <div style="top: 390px; left: 150px; width: 130px; text-align:center;" class="formtext"><?php echo $kun_curroccupation; ?></div>
            <div style="top: 390px; left: 535px; width: 140px; text-align:center;" class="formtext"><?php echo $kun_curroccupation; ?></div>
            <div style="top: 418px; left: 245px; width: 430px; text-align:center;" class="formtext"><?php echo $kun_address; ?></div>
            <div style="top: 450px; left: 175px; width: 475px; text-align:center;" class="formtext"><?php echo $kun_langspoken; ?></div>
            <div style="top: 510px; left: 105px; width: 160px; text-align:center;" class="formtext"><?php echo $kun_spnationality; ?></div>
            <div style="top: 510px; left: 473px; width: 190px; text-align:center;" class="formtext"><?php echo $kun_spname; ?></div>
            <div style="top: 535px; left: 105px; width: 160px; text-align:center;" class="formtext"><?php echo $kun_spoccupation; ?></div>
            <?php
			$kun_spdob = printdateblanker($kun_spdob);
			?>
            <div style="top: 535px; left: 473px; width: 43px; text-align: center;" class="formtext"><?php echo $kun_spdob[0]; ?></div>
            <div style="top: 535px; left: 525px; width: 35px; text-align: center;" class="formtext"><?php echo $kun_spdob[1]; ?></div>
          <div style="top: 535px; left: 570px; width: 35px; text-align: center;" class="formtext"><?php echo $kun_spdob[2]; ?></div>
          <div style="top: 535px; left: 608px; width: 55px; text-align: center;" class="formtext"><?php echo $kun_sppob; ?></div>
            <div style="top: 558px; left: 200px; width: 433px; text-align: center;" class="formtext"><?php echo $kun_spoccupation; ?></div>
            <div style="top: 618px; left: 128px; width: 151px; text-align: center;" class="formtext"><?php echo $kun_countryofissue; ?></div>
      <div style="top: 618px; left: 500px; width: 161px; text-align: center;" class="formtext"><?php echo $kun_ppno; ?></div>
      	<?php
			$kun_ppissue = printdateblanker($kun_ppissue);
			$kun_ppexpiry = printdateblanker($kun_ppexpiry);
			$kun_dateissuecerts = printdateblanker($kun_dateissuecerts);
		?>
      <div style="top: 641px; left: 129px; width: 54px; text-align: center;" class="formtext"><?php echo $kun_ppexpiry[0]; ?></div>
      <div style="top: 641px; left: 190px; width: 38px; text-align: center;" class="formtext"><?php echo $kun_ppexpiry[1]; ?></div>
      <div style="top: 641px; left: 235px; width: 38px; text-align: center;" class="formtext"><?php echo $kun_ppexpiry[2]; ?></div>
      <div style="top: 641px; left: 498px; width: 60px; text-align: center;" class="formtext"><?php echo $kun_ppissue[0]; ?></div>
      <div style="top: 641px; left: 568px; width: 50px; text-align: center;" class="formtext"><?php echo $kun_ppissue[1]; ?></div>
      <div style="top: 641px; left: 628px; width: 34px; text-align: center;" class="formtext"><?php echo $kun_ppissue[2]; ?></div>
      <div style="top: 665px; left: 325px; width: 34px; text-align: center;" class="formtext"><?php echo $kun_accomppersons; ?></div>
      <div style="top: 727px; left: 500px; width: 157px; text-align: center;" class="formtext"><?php echo $kun_profession; ?></div>
      <div style="top: 727px; left: 129px; width: 157px; text-align: center;" class="formtext"><?php echo $kun_certs; ?></div>
      <div style="top: 751px; left: 177px; width: 484px; text-align: center;" class="formtext"><?php echo $kun_issuecerts; ?></div>
      <div style="top: 774px; left: 292px; width: 66px; text-align: center;" class="formtext"><?php echo $kun_dateissuecerts[0]; ?></div>
      <div style="top: 774px; left: 458px; width: 42px; text-align: center;" class="formtext"><?php echo $kun_dateissuecerts[1]; ?></div>
      <div style="top: 774px; left: 392px; width: 42px; text-align: center;" class="formtext"><?php echo $kun_dateissuecerts[2]; ?></div>
      <div style="top: 835px; left: 152px; width: 499px; text-align: center;" class="formtext">IRAQ EMBASSY COLOMBO SRI LANKA</div>
      <div style="top: 857px; left: 152px; width: 499px; text-align: center;" class="formtext">COLOMBO INTERNATIONAL AIRPORT</div>
      <div style="top: 881px; left: 152px; width: 499px; text-align: center;" class="formtext">NAJAF INTERNATIONAL AIRPORT</div>
      <div style="top: 908px; left: 175px; width: 475px; text-align: center;" class="formtext">ARBAEEN/PILGRIMAGE</div>
      <div style="top: 929px; left: 223px; width: 401px; text-align: center;" class="formtext">12</div>
      <div style="top: 990px; left: 213px; width: 376px; text-align: center;" class="formtext">FAIZE HUSSAINI, MAULANA ABBAS STREET, KARBALA</div>
      <div style="top: 1015px; left: 237px; width: 350px; text-align: center;" class="formtext">NONE</div>
      <div style="top: 1039px; left: 225px; width: 350px; text-align: center;" class="formtext">NONE</div>
    </div>
        <div class="page">
            <img src="pdf/kun_template_print_blank_high2.jpg" class="bg" />
            <div style="top: 48px; left: 238px; width: 351px; text-align: center;"><?php echo $kun_numpreventry; ?></div>
            <?php
			$kun_datelentry = printdateblanker($kun_datelentry);
			$kun_child1dob = printdateblanker($kun_child1dob);
			$kun_child2dob = printdateblanker($kun_child2dob);
			$kun_child3dob = printdateblanker($kun_child3dob);
			$kun_visit1date = printdateblanker($kun_visit1date);
			$kun_visit2date = printdateblanker($kun_visit2date);
			$kun_visit3date = printdateblanker($kun_visit3date);
			?>
            <div style="top: 71px; left: 322px; width: 66px; text-align: center;" class="formtext"><?php echo $kun_datelentry[0]; ?></div>
      <div style="top: 71px; left: 500px; width: 42px; text-align: center;" class="formtext"><?php echo $kun_datelentry[1]; ?></div>
      <div style="top: 71px; left: 422px; width: 42px; text-align: center;" class="formtext"><?php echo $kun_datelentry[2]; ?></div>
      <div style="top: 96px; left: 191px; width: 396px; text-align: center;" class="formtext">NONE</div>
      <div style="top: 119px; left: 241px; width: 311px; text-align: center;" class="formtext">NONE</div>
      <div style="top: 168px; left: 238px; width: 311px; text-align: center;" class="formtext">NO</div>
      <div style="top: 192px; left: 309px; width: 281px; text-align: center;" class="formtext">NO</div>
      <div style="top: 313px; left: 597px; width: 148px; text-align: center;" class="formtext"><?php echo $kun_child1name; ?></div>
      <div style="top: 313px; left: 443px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_child1dob[0]; ?></div>
      <div style="top: 313px; left: 548px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_child1dob[1]; ?></div>
      <div style="top: 313px; left: 505px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_child1dob[2]; ?></div>
      <div style="top: 313px; left: 39px; width: 380px; text-align: center;" class="formtext"><?php echo $kun_child1occ; ?></div>
      <div style="top: 336px; left: 597px; width: 148px; text-align: center;" class="formtext"><?php echo $kun_child2name; ?></div>
      <div style="top: 336px; left: 443px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_child2dob[0]; ?></div>
      <div style="top: 336px; left: 548px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_child2dob[1]; ?></div>
      <div style="top: 336px; left: 505px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_child2dob[2]; ?></div>
      <div style="top: 336px; left: 39px; width: 380px; text-align: center;" class="formtext"><?php echo $kun_child2occ; ?></div>
      <div style="top: 360px; left: 597px; width: 148px; text-align: center;" class="formtext"><?php echo $kun_child3name; ?></div>
      <div style="top: 360px; left: 443px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_child3dob[0]; ?></div>
      <div style="top: 360px; left: 548px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_child3dob[1]; ?></div>
      <div style="top: 360px; left: 505px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_child3dob[2]; ?></div>
      <div style="top: 360px; left: 39px; width: 380px; text-align: center;" class="formtext"><?php echo $kun_child3occ; ?></div>
      <div style="top: 481px; left: 596px; width: 150px; text-align: center;" class="formtext"><?php echo $kun_visit1country; ?></div>
      <div style="top: 481px; left: 274px; width: 303px; text-align: center;" class="formtext"><?php echo $kun_visit1reason; ?></div>
      <div style="top: 481px; left: 65px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_visit1date[0]; ?></div>
      <div style="top: 481px; left: 144px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_visit1date[1]; ?></div>
      <div style="top: 481px; left: 203px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_visit1date[2]; ?></div>
      
      
      <div style="top: 504px; left: 596px; width: 150px; text-align: center;" class="formtext"><?php echo $kun_visit2country; ?></div>
      <div style="top: 504px; left: 274px; width: 303px; text-align: center;" class="formtext"><?php echo $kun_visit2reason; ?></div>
      <div style="top: 504px; left: 65px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_visit2date[0]; ?></div>
      <div style="top: 504px; left: 144px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_visit2date[1]; ?></div>
      <div style="top: 504px; left: 203px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_visit2date[2]; ?></div>
      
      <div style="top: 527px; left: 596px; width: 150px; text-align: center;" class="formtext"><?php echo $kun_visit3country; ?></div>
      <div style="top: 527px; left: 274px; width: 303px; text-align: center;" class="formtext"><?php echo $kun_visit3reason; ?></div>
      <div style="top: 527px; left: 65px; width: 51px; text-align: center;" class="formtext"><?php echo $kun_visit3date[0]; ?></div>
      <div style="top: 527px; left: 144px; width: 33px; text-align: center;" class="formtext"><?php echo $kun_visit3date[1]; ?></div>
      <div style="top: 527px; left: 203px; width: 36px; text-align: center;" class="formtext"><?php echo $kun_visit3date[2]; ?></div>
      
      <div style="top: 647px; left: 548px; width: 196px; text-align: center;" class="formtext">FAIZE HUSSAINI</div>
      <div style="top: 647px; left: 369px; width: 160px; text-align: center;" class="formtext">IRAQI</div>
      <div style="top: 647px; left: 38px; width: 311px; text-align: center;" class="formtext">MAULANA ABBAS STREET, KARBALA</div>
    </div> 
        <div class="noprint">
            <a href="<?php echo $server_link . "safars/kun-safar/$safar_id/edit-registration/$kunid"; ?>" class="btn btn-warning">Edit Registration</a>
            <a href="#" class="btn docuprint">Print</a>
        </div>
        <script src="<?php echo $server_link; ?>scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {
                $(".docuprint").click(function (e){
                    e.preventDefault();
                    window.print();
                    $.ajax({url: "update_print.php?kunid=<?php echo $kunid; ?>", success: function(result){}});
                });
            });
        </script>
    </body>
</html>
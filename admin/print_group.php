<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';
?>
    <!doctype html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>
            Printing Group
        </title>
        <style>
            @page {
              size: A4;
              margin: 0;
            }
            .page {
                position:relative;
                width: 210mm;
                height: 297mm;
                font-size: 15px;
                overflow: hidden;
            }
            table,
            td,
            th {
                padding: 3px;
                background: #fff;
                border: 1px solid #000;
                vertical-align: middle;
                height: 20px;
            }
            
            td{
                text-align: center;
            }

            table {
                background: #fff;
                border-collapse: collapse;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto
            }

            th {
                background: #eee;
                height: 18px;
                font-size: 14px;
            }

            th p{
                margin: 2px;
            }

            thead {
                display: table-header-group
            }
            
            td.photo_td{
                width: 1.46in;
                height: 1.4in;
            }
            
            td.name_td{
                width: 1.9in;
            }
            
            td.dob_td{
            }
            
            td.profession_td{
                width: 1.25in;
            }
            
            td.pp_td{
                width: 1.25in;
            }
            
            @media print {
                .page{
                    width: 210mm;
                    height: 297mm;
                    margin:0;
                    padding:0;
                    padding-top:10px;
                }
                .noprint{
                    display: none;
                }
            }

        </style>
    </head>

    <body>
        
        <?php 
        $group_data = array();

        $itsList = preg_split('/\r\n|\r|\n/', $_POST['itsList']);
        $safarid = $_GET['safarid'];
        if(isset($_POST['manifest']) && $_POST['manifest'] != '0') {
            $manifest = $_POST['manifest'];
            $sql_list = "SELECT kun_its, kun_fname, kun_mname, kun_lname, kun_dob, kun_birthplace, kun_nationality, kun_profession, kun_ppno, kun_ppissue, kun_ppexpiry 
                        FROM zyt_kun_registrations 
                        WHERE zyt_kun_manifest = $manifest AND safar_id = $safarid
                        ORDER BY zyt_kun_priority";
            $result_list = dbQuery($sql_list);
            while($row = dbFetchAssoc($result_list))
            {
                $group_data[] = $row;
            }
        } else {
            foreach($itsList as $its){
                $sql_list = "SELECT kun_its, kun_fname, kun_mname, kun_lname, kun_dob, kun_birthplace, kun_nationality, kun_profession, kun_ppno, kun_ppissue, kun_ppexpiry 
                        FROM zyt_kun_registrations 
                        WHERE kun_its = $its AND safar_id = $safarid
                        ORDER BY zyt_kun_priority";
                $result_list = dbQuery($sql_list);    
                while($row = dbFetchAssoc($result_list))
                {
                    $group_data[] = $row;
                }
            }
        }
                
        $groupsize = sizeof($group_data);
        $iter = 0;

        //figure out the number of tables needed if each table needs 5 ppl printed
        $num_tables = ceil($groupsize/6); //gets the upper table value
        for($tbl = 1; $tbl <= $num_tables; $tbl++){
            $curr_set = $tbl * 6; //this defines where the loop has to end per table
            ?>
        <div class="page">
            <div style="padding:20px;">
                <table width="100%" border="1" cellspacing="0" cellpadding="2">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>
                                <p>Date of Birth <br> &amp; Place</p>
                                <p>Nationality</p>
                            </th>
                            <th>Profession</th>
                            <th>
                                <p>Passport No.<br> Date of Issue<br> Date of Expiry</p>
                            </th>
                            <th>Photograph</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = $iter; $i < $curr_set; $i++){
                        if(!isset($group_data[$i]['kun_fname'])) break;
                        ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td class="name_td"><?php echo strtoupper(strtolower($group_data[$i]['kun_fname'])) . " " . strtoupper(strtolower($group_data[$i]['kun_mname'])) . " " . strtoupper(strtolower($group_data[$i]['kun_lname'])); ?></td>
                            <td class="dob_td"><?php echo embassy_formatter($group_data[$i]['kun_dob']); ?><br /><br />
                                <?php echo strtoupper(strtolower($group_data[$i]['kun_birthplace'])); ?><br /><br /><?php echo strtoupper(strtolower($group_data[$i]['kun_nationality'])); ?></td>
                            <td class="profession_td"><?php echo strtoupper(strtolower($group_data[$i]['kun_profession'])); ?></td>
                            <td class="pp_td"><?php echo strtoupper($group_data[$i]['kun_ppno']); ?><br /><br /><?php echo embassy_formatter($group_data[$i]['kun_ppissue']); ?><br /><br /><?php echo embassy_formatter($group_data[$i]['kun_ppexpiry']); ?></td>
                            <td class="photo_td">&nbsp;</td>
                        </tr>
                        <?php
                        $iter++;
                        }
                        ?>
                    </tbody>
                </table>
                <div style="text-align:center; margin-top:20px;"><?php echo "Page $tbl of $num_tables"; ?></div>
            </div>
        </div>
        <?php
        }
        ?>
    </body>
</html>

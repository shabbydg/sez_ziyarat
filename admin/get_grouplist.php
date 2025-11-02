<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';
?>

<h3>Select from Dropdown or enter ITS numbers one below the other.</h3>
<form action="<?php echo $server_link; ?>print_group.php?safarid=<?php echo $safarid; ?>" method="post">
    <textarea name="itsList" rows="5" placeholder="ITS numbers..."></textarea><br />
    <select name="manifest">
        <option value="0">--Select Group --</option>
        <option value="1">Group 1</option>
        <option value="2">Group 2</option>
        <option value="3">Group 3</option>
        <option value="4">Group 4</option>
        <option value="5">Group 5</option>
        <option value="6">Group 6</option>
        <option value="7">Group 7</option>
        <option value="8">Group 8</option>
        <option value="9">Group 9</option>
        <option value="10">Group 10</option>
        <option value="11">Group 11</option>
        <option value="12">Group 12</option>
        <option value="13">Group 13</option>
        <option value="14">Group 14</option>
        <option value="15">Group 15</option>
    </select>
    <input type="submit" value="Submit & Print" />
</form>
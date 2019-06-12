<?PHP
    error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once('../import/assembldb.php');

    require("../import/sessionstart.php");

    function encodeURIComponent($str) {
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode($str), $revert);
	}

    if ($_GET["step"] == "sendmail") {
        $_SESSION["reset_details"] = array();
        $_SESSION["reset_errors"] = array();

        if (isset($_POST["g-recaptcha-response"]) && !empty($_POST["g-recaptcha-response"])) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $remoteip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
                $remoteip = $_SERVER['REMOTE_ADDR'];
            }
            $data = array('secret' => '***REMOVED_G_RECAPTCHA_SECRET***', 'response' => $_POST["g-recaptcha-response"], 'remoteip' => $remoteip);

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === false) {
                $_SESSION["reset_errors"]["captcha"] = "Could not validate whether or not you are a bot using reCAPTCHA. If you're using a VPN or a proxy, please turn it off and try again.";
            }
            else {
                $responseJSON = json_encode($result);
                $responseJSON = json_decode($result, true);
                if ($responseJSON["success"] === true) {
                    // EMAIL field
                    if (isset($_POST["reset-form-email"]) && !empty($_POST["reset-form-email"])) {
                        if (empty(trim($_POST["reset-form-email"]))) {
                            $_SESSION["reset_errors"]["email"] = "E-mail address cannot be empty";
                        }
                        else {
                            if (filter_var($_POST["reset-form-email"], FILTER_VALIDATE_EMAIL)) {
                                $uid = AssemblDB::getUIDByEmail($_POST["reset-form-email"]);
                                if ($uid !== false) {
                                    $_SESSION["reset_details"]["email"] = $_POST["reset-form-email"];

                                    $connection = AssemblDB::getAccountsConnection();
                                    $sql = "SELECT * FROM `users`.`userdata` WHERE uid='".AssemblDB::makeSafe($uid, $connection)."' LIMIT 1";
                                    $result = mysqli_query($connection, $sql);
                                    $userData = mysqli_fetch_assoc($result);

                                    $resetCode = AssemblDB::createID(128);
                                    $resetCodeValidUntil = strtotime("+2 hour");
                                    $sql = "UPDATE `users`.`accounts` SET `reset_code`='".AssemblDB::makeSafe($resetCode, $connection)."', `reset_code_expires`=FROM_UNIXTIME(".$resetCodeValidUntil.") WHERE uid='".AssemblDB::makeSafe($uid, $connection)."' LIMIT 1";
                                    $result = mysqli_query($connection, $sql);

                                    require_once('../import/phpmailer/PHPMailerAutoload.php');
                                    require_once('../import/maillogin.php');

                                    $mail = new PHPMailer;

                                    mailLogin($mail);
                                    
                                    $mail->addAddress($userData["email_address"], $userData["name"]);
                                    
                                    $mail->isHTML(false);
                                    $mail->Subject = "Reset the password for your Assembl account";
                                    $mail->Body = "Hi ".$userData["name"].",\n\nhere is the link to reset the password for your Assembl account:\n\nhttps://accounts.assembl.ch/passwordreset/?step=code&code=".$resetCode."&continue=".encodeURIComponent($_GET["continue"])."\n\nThis link will be valid for two hours. If you did not request to reset your password, you can safely ignore this e-mail.\n\nKind regards,\nThe Assembl Team";

                                    $sent = $mail->send();

                                    if ($sent === false) {
                                        $_SESSION["reset_errors"]["general"] = "Could not send e-mail. Try again later.";
                                    }
                                    else {
                                        $_SESSION["pw_reset_mail_sent"] = true;
                                        header("Location: /passwordreset/?step=mailsent");
                                        die();
                                    }
                                }
                                else {
                                    $_SESSION["reset_details"]["email"] = $_POST["reset-form-email"];
                                    $_SESSION["reset_errors"]["email"] = "This e-mail address has no Assembl account linked to it. Go and register instead!";
                                }
                            }
                            else {
                                $_SESSION["reset_errors"]["email"] = "Invalid e-mail address";
                                $_SESSION["reset_details"]["email"] = $_POST["reset-form-email"];
                            }
                        }
                    }
                    else {
                        $_SESSION["reset_errors"]["email"] = "E-mail address cannot be empty";
                    }
                }
                else {
                    $_SESSION["reset_errors"]["captcha"] = "According to reCAPTCHA, you might be a bot. Currently, we do not allow for bots to create or use Assembl accounts.";
                }
            }
        }
        else {
            $_SESSION["reset_errors"]["captcha"] = "Please confirm you're not a bot by completing the reCAPTCHA below";
        }

        if (count($_SESSION["reset_errors"]) > 0) {
            // errors have been found. Do not proceed unless these errors have all been fixed
            header("Location: /passwordreset/?step=sendmail&continue=".encodeURIComponent($_GET["continue"]));
            die();
        }
    }
    else if ($_GET["step"] == "pwchangecode" && isset($_SESSION["pw_reset_uid"]) && !empty($_SESSION["pw_reset_uid"])) {
        $_SESSION["reset_details"] = array();
        $_SESSION["reset_errors"] = array();

        // PASSWORD field
        if (isset($_POST["reset-form-password"]) && !empty($_POST["reset-form-password"])) {
            if (empty(trim($_POST["reset-form-password"]))) {
                $_SESSION["reset_errors"]["general"] = "Password cannot be empty";
            }
            else {
                if (AssemblDB::passwordMeetsRequirements($_POST["reset-form-password"])) {
                    // PASSWORD-CHECK field
                    if (isset($_POST["reset-form-password-check"]) && !empty($_POST["reset-form-password-check"])) {
                        if (empty(trim($_POST["reset-form-password-check"]))) {
                            $_SESSION["reset_errors"]["general"] = "Please re-enter your password and confirm it below";
                        }
                        else {
                            if ($_POST["reset-form-password"] === $_POST["reset-form-password-check"]) {
                                $hashedPw = AssemblDB::hashPassword($_POST["reset-form-password"]);
                                
                                $connection = AssemblDB::getAccountsConnection();
                                $sql = "SELECT password FROM `users`.`accounts` WHERE uid='".AssemblDB::makeSafe($_SESSION["pw_reset_uid"], $connection)."' LIMIT 1";
                                $result = mysqli_query($connection, $sql);
                                $dbPassword = mysqli_fetch_assoc($result)["password"];
                                if (!password_verify($_POST["reset-form-password"], $dbPassword)) {
                                    $sql = "UPDATE `users`.`accounts` SET `password`='".AssemblDB::makeSafe($hashedPw, $connection)."', `timestamp_set`=CURRENT_TIMESTAMP() WHERE uid='".AssemblDB::makeSafe($_SESSION["pw_reset_uid"], $connection)."' LIMIT 1";
                                    $result = mysqli_query($connection, $sql);

                                    $sql = "SELECT email_address, name FROM `users`.`userdata` WHERE uid='".AssemblDB::makeSafe($_SESSION["pw_reset_uid"], $connection)."' LIMIT 1";
                                    $result = mysqli_query($connection, $sql);
                                    $userData = mysqli_fetch_assoc($result);

                                    if (!isset($_SESSION["pw_change_required"]) || $_SESSION["pw_change_required"] != true) {
                                        require_once('../import/phpmailer/PHPMailerAutoload.php');
                                        require_once('../import/maillogin.php');

                                        $mail = new PHPMailer;

                                        mailLogin($mail);
                                        
                                        $mail->addAddress($userData["email_address"], $userData["name"]);
                                        
                                        $mail->isHTML(false);
                                        $mail->Subject = "Your password has been changed";
                                        $mail->Body = "Hi ".$userData["name"].",\n\nthe password for your Assembl account has just been changed. If this wasn't you, please reset your password at https://accounts.assembl.ch/passwordreset/. If this was you, you can safely ignore this e-mail.\n\nKind regards,\nThe Assembl Team";

                                        $mail->send();
                                    }
                                    else {
                                        $sql = "UPDATE `users`.`accounts` SET `needs_reset`=0 WHERE uid='".AssemblDB::makeSafe($_SESSION["pw_reset_uid"], $connection)."' LIMIT 1";
                                        $result = mysqli_query($connection, $sql);
                                    }

                                    unset($_SESSION["pw_reset_uid"]);
                                    $_SESSION["pw_reset_success"] = true;

                                    header("Location: /passwordreset/?step=confirm&continue=".encodeURIComponent($_GET["continue"]));
                                    die();
                                }
                                else {
                                    $_SESSION["reset_errors"]["general"] = "New password cannot be the same as the one currently in use";
                                }
                            }
                            else {
                                $_SESSION["reset_errors"]["general"] = "Passwords did not match";
                            }
                        }
                    }
                    else {
                        $_SESSION["reset_errors"]["general"] = "Please re-enter your password and confirm it below";
                    }
                }
                else {
                    $_SESSION["reset_errors"]["general"] = "New password must meet one of the following requirements: <ul>";
                    $reqs = AssemblDB::passwordRequirements();
                    for ($i = 0; $i < count($reqs); $i++) {
                        $_SESSION["reset_errors"]["general"] .= "<li>" . $reqs[$i] . "</li>";
                    }
                    $_SESSION["reset_errors"]["general"] .= "</ul>";
                }
            }
        }
        else {
            $_SESSION["reset_errors"]["general"] = "Password cannot be empty";
        }

        if (count($_SESSION["reset_errors"]) > 0) {
            // errors have been found. Do not proceed unless these errors have all been fixed
            header("Location: /passwordreset/?step=code&continue=".encodeURIComponent($_GET["continue"]));
            die();
        }
    }
    else {
        header("Location: /passwordreset/");
        die();
    }
?>
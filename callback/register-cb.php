<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once('../import/assembldb.php');

    require("../import/sessionstart.php");

    function encodeURIComponent($str) {
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode($str), $revert);
	}

    function sendVerifCode($code, $email, $name) {
        require_once('../import/phpmailer/PHPMailerAutoload.php');
        require_once('../import/maillogin.php');

        $mail = new PHPMailer;

        mailLogin($mail);
        
        $mail->addAddress($email, $name);
        
        $mail->isHTML(false);
        $mail->Subject = "Verify your e-mail address";
        $mail->Body = "Hi ".$name.",\n\nyou just signed up for an Assembl account. To verify your e-mail address, please enter the following code:\n\n".$code."\n\nKind regards,\nThe Assembl Team\n\n\nP.S.: if you did not sign up for an account, you can safely ignore this e-mail.";

        return $mail->send();
    }
    
    if (intval($_GET["step"]) == 1) {
        $_SESSION["register_details"] = array();
        $_SESSION["register_errors"] = array();

        // NAME field
        if (isset($_POST["register-form-name"]) && !empty($_POST["register-form-name"])) {
            if (empty(trim($_POST["register-form-name"]))) {
                $_SESSION["register_errors"]["name"] = "Name cannot be empty";
            }
            else {
                $_SESSION["register_details"]["name"] = $_POST["register-form-name"];
            }
        }
        else {
            $_SESSION["register_errors"]["name"] = "Name cannot be empty";
        }

        // EMAIL field
        if (isset($_POST["register-form-email"]) && !empty($_POST["register-form-email"])) {
            if (empty(trim($_POST["register-form-email"]))) {
                $_SESSION["register_errors"]["email"] = "E-mail address cannot be empty";
            }
            else {
                if (filter_var($_POST["register-form-email"], FILTER_VALIDATE_EMAIL)) {
                    if (AssemblDB::getUIDByEmail($_POST["register-form-email"]) === false) {
                        $_SESSION["register_details"]["email"] = $_POST["register-form-email"];
                    }
                    else {
                        $_SESSION["register_errors"]["email"] = "An account with this e-mail address already exists";
                        $_SESSION["register_details"]["email"] = $_POST["register-form-email"];
                    }
                }
                else {
                    $_SESSION["register_errors"]["email"] = "Invalid e-mail address";
                    $_SESSION["register_details"]["email"] = $_POST["register-form-email"];
                }
            }
        }
        else {
            $_SESSION["register_errors"]["email"] = "E-mail address cannot be empty";
        }

        // BIRTH-DATE field
        if (isset($_POST["register-form-birth-date"]) && !empty($_POST["register-form-birth-date"])) {
            if (empty(trim($_POST["register-form-birth-date"]))) {
                $_SESSION["register_errors"]["birth-date"] = "Birth date cannot be empty";
            }
            else {
                $birthDate = strtotime($_POST["register-form-birth-date"]);
                if ($birthDate) {
                    $longAgoDate = strtotime("-150 year -1 day");
                    $today = strtotime("+1 day");
                    if ($birthDate < $longAgoDate) {
                        $_SESSION["register_errors"]["birth-date"] = "Are you sure you are over 150 years old...?";
                    }
                    else if ($birthDate > $today) {
                        $_SESSION["register_errors"]["birth-date"] = "You are born in the future? How are the flying cars?";
                    }
                    else {
                        $birthDate = date('Y-m-d', $birthDate);
                        $_SESSION["register_details"]["birth-date"] = $birthDate;
                    }
                }
                else {
                    $_SESSION["register_errors"]["birth-date"] = "Invalid birth date";
                }
            }
        }
        else {
            $_SESSION["register_errors"]["birth-date"] = "Birth date cannot be empty";
        }

        // PASSWORD field
        if (isset($_POST["register-form-password"]) && !empty($_POST["register-form-password"])) {
            if (empty(trim($_POST["register-form-password"]))) {
                $_SESSION["register_errors"]["password"] = "Password cannot be empty";
            }
            else {
                if (AssemblDB::passwordMeetsRequirements($_POST["register-form-password"])) {
                    // PASSWORD-CHECK field
                    if (isset($_POST["register-form-password-check"]) && !empty($_POST["register-form-password-check"])) {
                        if (empty(trim($_POST["register-form-password-check"]))) {
                            $_SESSION["register_errors"]["password"] = "Please re-enter your password and confirm it below";
                            $_SESSION["register_errors"]["password-check"] = "Please confirm your password here";
                        }
                        else {
                            if ($_POST["register-form-password"] === $_POST["register-form-password-check"]) {
                                $hashedPw = AssemblDB::hashPassword($_POST["register-form-password"]);
                                if (empty($hashedPw)) {
                                    $_SESSION["register_errors"]["general"] = "Could not hash password";
                                }
                                else {
                                    $_SESSION["register_details"]["password"] = $hashedPw;
                                }
                            }
                            else {
                                $_SESSION["register_errors"]["password"] = "Passwords did not match";
                                $_SESSION["register_errors"]["password-check"] = "Passwords did not match";
                            }
                        }
                    }
                    else {
                        $_SESSION["register_errors"]["password"] = "Please re-enter your password and confirm it below";
                        $_SESSION["register_errors"]["password-check"] = "Please confirm your password here";
                    }
                }
                else {
                    $_SESSION["register_errors"]["password"] = "Password must meet one of the following requirements: <ul>";
                    $reqs = AssemblDB::passwordRequirements();
                    for ($i = 0; $i < count($reqs); $i++) {
                        $_SESSION["register_errors"]["password"] .= "<li>" . $reqs[$i] . "</li>";
                    }
                    $_SESSION["register_errors"]["password"] .= "</ul>";
                }
            }
        }
        else {
            $_SESSION["register_errors"]["password"] = "Password cannot be empty";
        }

        // TERMS checkbox
        if (isset($_POST["register-form-terms"]) && !empty($_POST["register-form-terms"])) {
            if ($_POST["register-form-terms"] !== "true") {
                $_SESSION["register_errors"]["terms"] = "You must agree with our terms &amp; conditions before you can register for an account";
            }
            else {
                $_SESSION["register_details"]["terms"] = true;
            }
        }
        else {
            $_SESSION["register_errors"]["terms"] = "You must agree with our terms &amp; conditions before you can register for an account";
        }

        // CAPTCHA
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
                $_SESSION["register_errors"]["captcha"] = "Could not validate whether or not you are a bot using reCAPTCHA. If you're using a VPN or a proxy, please turn it off and try again.";
            }
            else {
                $responseJSON = json_encode($result);
                $responseJSON = json_decode($result, true);
                if ($responseJSON["success"] === true) {
                    // do nothing, just continue
                }
                else {
                    $_SESSION["register_errors"]["captcha"] = "According to reCAPTCHA, you might be a bot. Currently, we do not allow for bots to create or use Assembl accounts.";
                }
            }
        }
        else {
            $_SESSION["register_errors"]["captcha"] = "Please confirm you're not a bot by completing the reCAPTCHA below";
        }

        if (count($_SESSION["register_errors"]) > 0) {
            // errors have been found. Do not proceed register process unless these errors have all been fixed
            header("Location: /register/?step=1&continue=".encodeURIComponent($_GET["continue"]));
            die();
        }
        else {
            // all errors have been fixed or no errors have been found. Proceed to register
            $_SESSION["email_verif_code"] = random_int(100000, 999999);

            $_SESSION["verification_errors"] = array();
            $_SESSION["verification_incorrect_attempts"] = 0;

            $sent = sendVerifCode($_SESSION["email_verif_code"], $_SESSION["register_details"]["email"], $_SESSION["register_details"]["name"]);

            if ($sent === false) {
                $_SESSION["register_errors"]["general"] = "Could not send verification e-mail. Try again later.";
                header("Location: /register/?step=1&continue=".encodeURIComponent($_GET["continue"]));
                die();
            }
            else {
                header("Location: /register/?step=2&continue=".encodeURIComponent(($_GET["continue"]));
                die();
            }
        }
    }
    else if (intval($_GET["step"]) == 2 && isset($_SESSION["email_verif_code"]) && !empty($_SESSION["email_verif_code"])) {
        $_SESSION["verification_errors"] = array();

        // CODE field
        if (isset($_POST["verification-form-code"]) && !empty($_POST["verification-form-code"])) {
            if (empty(trim($_POST["verification-form-code"]))) {
                $_SESSION["verification_errors"]["code"] = "Please enter the verification code we've sent to you via e-mail";
            }
            else {
                if (intval($_POST["verification-form-code"]) == $_SESSION["email_verif_code"]) {
                    // proceed registering, code was correct. 
                }
                else {
                    $_SESSION["verification_incorrect_attempts"] += 1;
                    if ($_SESSION["verification_incorrect_attempts"] >= 4) {
                        unset($_SESSION["email_verif_code"]);
                        $_SESSION["register_details"] = array();
                        $_SESSION["register_errors"] = array();
                        $_SESSION["register_errors"]["general"] = "You entered an incorrect verification code 4 times. Out of precaution, we've blocked this registration process. You can try again now. If you need any help, feel free to contact Assembl Support.";
                        $_SESSION["verification_errors"] = array();
                        $_SESSION["verification_incorrect_attempts"] = 0;
                        header("Location: /register/?step=1&continue=".encodeURIComponent(($_GET["continue"]));
                        die();
                    }
                    else {
                        $attemptsRemaining = 4 - $_SESSION["verification_incorrect_attempts"];
                        $_SESSION["verification_errors"]["code"] = "Incorrect code. " . $attemptsRemaining . " attempt".($attemptsRemaining == 1 ? "" : "s")." remaining.";
                    }
                }
            }
        }
        else {
            $_SESSION["verification_errors"]["code"] = "Please enter the verification code we've sent to you via e-mail";
        }

        if (count($_SESSION["verification_errors"]) > 0) {
            // errors have been found. Do not proceed register process unless these errors have all been fixed
            header("Location: /register/?step=2&continue=".encodeURIComponent($_GET["continue"]));
            die();
        }
        else {
            $uid = AssemblDB::createUID();
            $connection = AssemblDB::getSetupConnection();

            function horriblyWrong() {
                unset($_SESSION["email_verif_code"]);
                $_SESSION["register_details"] = array();
                $_SESSION["register_errors"] = array();
                $_SESSION["register_errors"]["general"] = "Something went horribly wrong. Please try again.";
                $_SESSION["verification_errors"] = array();
                $_SESSION["verification_incorrect_attempts"] = 0;
                header("Location: /register/?step=1&continue=".encodeURIComponent($_GET["continue"]));
                die();
            }

            $sql = "INSERT INTO `users`.`accounts` (uid, password, timestamp_set, needs_reset) VALUES ('".$uid."', '".AssemblDB::makeSafe($_SESSION["register_details"]["password"], $connection)."', CURRENT_TIMESTAMP(), 0)";
            $result = mysqli_query($connection, $sql);
            if ($result === false) {
                horriblyWrong();
            }

            $sql = "INSERT INTO `users`.`userdata` (uid, name, birth_date, registered_on, email_address) VALUES ('".$uid."', '".AssemblDB::makeSafe($_SESSION["register_details"]["name"], $connection)."', STR_TO_DATE('".AssemblDB::makeSafe($_SESSION["register_details"]["birth-date"], $connection)."', '%Y-%m-%d'), CURRENT_TIMESTAMP(), '".AssemblDB::makeSafe($_SESSION["register_details"]["email"], $connection)."')";
            $result = mysqli_query($connection, $sql);
            if ($result === false) {
                horriblyWrong();
            }

            $sql = "INSERT INTO `users`.`orcid` (uid) VALUES ('".$uid."')";
            $result = mysqli_query($connection, $sql);
            if ($result === false) {
                horriblyWrong();
            }

            $sql = "INSERT INTO `users`.`application_keys` (uid) VALUES ('".$uid."')";
            $result = mysqli_query($connection, $sql);
            if ($result === false) {
                horriblyWrong();
            }
            
            require("../import/sessionend.php");
            require("../import/sessionstart.php");
            $_SESSION["account_created"] = true;

            header("Location: /register/?step=3&continue=".encodeURIComponent($_GET["continue"]));
            die();
        }
    }
    else {
        header("Location: /register/?step=1");
        die();
    }
?>
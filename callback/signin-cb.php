<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once('../import/assembldb.php');

    session_start();

    if ($_GET["step"] == "init") {
        $_SESSION["signin_details"] = array();
        $_SESSION["signin_errors"] = array();

        $actuallyCheckPassword = false;
        $uid = false;
        $uaKey = AssemblDB::getUserAgentKey($_SERVER['HTTP_USER_AGENT']);

        function incorrectPasswordCombo($uaKey, $email, $captchaSuccess, $connection) {
            $_SESSION["signin_errors"]["general"] = "Incorrect e-mail and password combination";
            $sql = "INSERT INTO `failed_attempts`.`logins` (email_address, timestamp, ip_address, user_agent_key, captcha_success) VALUES ('".AssemblDB::makeSafe($email, $connection)."', CURRENT_TIMESTAMP(), '".AssemblDB::makeSafe($_SERVER['REMOTE_ADDR'], $connection)."', '".$uaKey."', 1)";
            $result = mysqli_query($connection, $sql);
            if (!isset($_SESSION["signin_incorrect_attempts"])) {
                $_SESSION["signin_incorrect_attempts"] = 1;
            }
            else {
                $_SESSION["signin_incorrect_attempts"] += 1;
            }

            if ($_SESSION["signin_incorrect_attempts"] >= 10) {
                $_SESSION["signin_captcha_required"] = true;
            }
            return $_SESSION["signin_incorrect_attempts"];
        }

        if (isset($_SESSION["signin_captcha_required"]) && $_SESSION["signin_captcha_required"] === true) {
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
                    $_SESSION["signin_errors"]["captcha"] = "Could not validate whether or not you are a bot using reCAPTCHA. If you're using a VPN or a proxy, please turn it off and try again.";
                }
                else {
                    $responseJSON = json_encode($result);
                    $responseJSON = json_decode($result, true);
                    if ($responseJSON["success"] === true) {
                        // do nothing, just continue
                    }
                    else {
                        $_SESSION["signin_errors"]["captcha"] = "According to reCAPTCHA, you might be a bot. Currently, we do not allow for bots to create or use Assembl accounts.";
                    }
                }
            }
            else {
                $_SESSION["signin_errors"]["captcha"] = "Please confirm you're not a bot by completing the reCAPTCHA below";
            }
        }

        // EMAIL field
        if (isset($_POST["signin-form-email"]) && !empty($_POST["signin-form-email"])) {
            if (empty(trim($_POST["signin-form-email"]))) {
                $_SESSION["signin_errors"]["email"] = "E-mail address cannot be empty";
            }
            else {
                if (filter_var($_POST["signin-form-email"], FILTER_VALIDATE_EMAIL)) {
                    $uid = AssemblDB::getUIDByEmail($_POST["signin-form-email"]);
                    if ($uid !== false) {
                        $_SESSION["signin_details"]["email"] = $_POST["signin-form-email"];
                        $actuallyCheckPassword = true;
                    }
                    else {
                        $_SESSION["signin_details"]["email"] = $_POST["signin-form-email"];
                    }
                }
                else {
                    $_SESSION["signin_errors"]["email"] = "Invalid e-mail address";
                    $_SESSION["signin_details"]["email"] = $_POST["signin-form-email"];
                }
            }
        }
        else {
            $_SESSION["signin_errors"]["email"] = "E-mail address cannot be empty";
        }

        // PASSWORD field
        if (isset($_POST["signin-form-password"]) && !empty($_POST["signin-form-password"])) {
            if (empty(trim($_POST["signin-form-password"]))) {
                $_SESSION["signin_errors"]["password"] = "Password cannot be empty";
            }
            else {
                $connection = AssemblDB::getLoginsConnection();
                if ($actuallyCheckPassword && count($_SESSION["signin_errors"]) == 0) {
                    $sql = "SELECT password FROM `users`.`accounts` WHERE uid='".AssemblDB::makeSafe($uid, $connection)."' LIMIT 1";
                    $result = mysqli_query($connection, $sql);
                    if ($result == false) {
                        // SQL error
                        $_SESSION["signin_errors"]["general"] = "Signing in is currently not possible. Sorry for any inconvenience caused.";
                    }
                    else if (mysqli_num_rows($result) > 0) {
                        // user found! Checking password...
                        $dbPassword = mysqli_fetch_assoc($result)["password"];
                        if (password_verify($_POST["signin-form-password"], $dbPassword)) {
                            $sql = "SELECT needs_reset FROM `users`.`accounts` WHERE uid='".AssemblDB::makeSafe($uid, $connection)."' LIMIT 1";
                            $result = mysqli_query($connection, $sql);
                            $passwordNeedsChanging = intval(mysqli_fetch_assoc($result)["needs_reset"]) > 0;
                            if (!$passwordNeedsChanging) {
                                // correct password and password does not require changing. Time to sign in!
                                $sql = "INSERT INTO `users`.`logins` (uid, timestamp, ip_address, user_agent_key) VALUES ('".AssemblDB::makeSafe($uid, $connection)."', CURRENT_TIMESTAMP(), '".AssemblDB::makeSafe($_SERVER['REMOTE_ADDR'], $connection)."', '".$uaKey."')";
                                $result = mysqli_query($connection, $sql);
                                
                                unset($_SESSION["signin_details"]);
                                unset($_SESSION["signin_errors"]);
                                if (isset($_SESSION["signin_captcha_required"])) {
                                    unset($_SESSION["signin_captcha_required"]);
                                }
                                if (isset($_SESSION["signin_incorrect_attempts"])) {
                                    unset($_SESSION["signin_incorrect_attempts"]);
                                }
                                $_SESSION["signed_in"] = true;

                                $connection = AssemblDB::getAccountsConnection();
                                $sql = "SELECT * FROM `users`.`userdata` WHERE uid='".AssemblDB::makeSafe($uid, $connection)."' LIMIT 1";
                                $result = mysqli_query($connection, $sql);
                                $_SESSION["userdata"] = mysqli_fetch_assoc($result);

                                header("Location: /signin/?step=signed_in");
                                die();
                            }
                            else {
                                // password needs changing. Do not sign in yet, but refer to the password reset page
                                $_SESSION["pw_reset_uid"] = $uid;
                                $_SESSION["pw_change_required"] = true;
                                $_SESSION["reset_errors"] = array();
                                $_SESSION["reset_errors"]["general"] = "Your password requires changing before you can sign in.";
                                header("Location: /passwordreset/?step=code");
                                die();
                            }
                        }
                        else {
                            // incorrect password
                            incorrectPasswordCombo($uaKey, $_SESSION["signin_details"]["email"], true, $connection);
                        }
                    }
                    else {
                        // somehow the account has not been found in the database even though the email address was...
                        $_SESSION["signin_errors"]["general"] = "Signing in is currently not possible. Sorry for any inconvenience caused.";
                    }
                }
                else {
                    if (count($_SESSION["signin_errors"]) == 0) {
                        // act as if password is being checked and stuff
                        sleep(1);
                        incorrectPasswordCombo($uaKey, $_SESSION["signin_details"]["email"], true, $connection);
                    }
                }
            }
        }
        else {
            $_SESSION["signin_errors"]["password"] = "Password cannot be empty";
        }

        if (count($_SESSION["signin_errors"]) > 0) {
            // errors have been found. Do not proceed signin process unless these errors have all been fixed
            header("Location: /signin/");
            die();
        }
    }
    else {
        header("Location: /signin/");
        die();
    }
?>
<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once('../import/assembldb.php');

    require("../import/sessionstart.php");

    $_SESSION["delete_details"] = array();
    $_SESSION["delete_errors"] = array();

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
            $_SESSION["delete_errors"]["captcha"] = "Could not validate whether or not you are a bot using reCAPTCHA. If you're using a VPN or a proxy, please turn it off and try again.";
        }
        else {
            $responseJSON = json_encode($result);
            $responseJSON = json_decode($result, true);
            if ($responseJSON["success"] === true) {
                // confirmation field
                if (isset($_POST["delete-form-confirm"]) && !empty($_POST["delete-form-confirm"])) {
                    if (trim($_POST["delete-form-confirm"]) === "DELETE") {
                        // understand checkbox
                        if (isset($_POST["delete-form-understand"]) && !empty($_POST["delete-form-understand"])) {
                            if ($_POST["delete-form-understand"] !== "true") {
                                $_SESSION["delete_errors"]["understand"] = "Please agree to the following:";
                            }
                            else {
                                // delete the account for good
                                $connection = AssemblDB::getAccountsConnection();

                                $sql = "DELETE FROM `users`.`accounts` WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                                $result = mysqli_query($connection, $sql);

                                header("Location: /signout/?del");
                                die();
                            }
                        }
                        else {
                            $_SESSION["delete_errors"]["understand"] = "Please agree to the following:";
                        }
                    }
                    else {
                        $_SESSION["delete_errors"]["confirm"] = "Please confirm you want to delete your account by writing DELETE in all capital letters below:";
                    }
                }
                else {
                    $_SESSION["delete_errors"]["confirm"] = "Please confirm you want to delete your account by writing DELETE in all capital letters below:";
                }
            }
            else {
                $_SESSION["delete_errors"]["captcha"] = "According to reCAPTCHA, you might be a bot. We will never allow bots to delete Assembl accounts.";
            }
        }
    }
    else {
        $_SESSION["delete_errors"]["captcha"] = "Please confirm you're not a bot by completing the reCAPTCHA below";
    }

    if (count($_SESSION["delete_errors"]) > 0) {
        // errors have been found. Do not proceed unless these errors have all been fixed
        header("Location: /delete/");
        die();
    }
?>
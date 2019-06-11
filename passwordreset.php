<?PHP
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Reset your Assembl password</title>
        <base href="https://accounts.assembl.ch/" />
        <link rel="stylesheet" href="/loginstyles.css" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script>
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
            results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
        </script>
    </head>
    <body>
        <div id="background-image"></div>
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <div style="display: none;" id="loading">
                        <img class="loading-svg" src="import/loading.svg" />
                    </div>
                    <script src="/import/loader.js"></script>
                    <h1>Assembl</h1>
                    <h2>Reset your password</h2>
                    <hr />
                    <?PHP if (isset($_SESSION["reset_errors"]) && isset($_SESSION["reset_errors"]["general"]) && !empty($_SESSION["reset_errors"]["general"])) { echo '<div class="form-error centered">' . $_SESSION["reset_errors"]["general"] . '</div><hr />'; } ?>
                    <?PHP if (!isset($_GET["step"]) || empty($_GET["step"]) || $_GET["step"] == "sendmail") { ?>
                        <form action="/callback/pwreset-cb/?step=sendmail" method="post" autocomplete="off">
                            <p>Enter your account's e-mail address and we'll send you a password reset link.</p>

                            <label for="reset-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["reset_errors"]) && isset($_SESSION["reset_errors"]["email"]) && !empty($_SESSION["reset_errors"]["email"])) { echo $_SESSION["reset_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" id="reset-form-email" name="reset-form-email" value="<?PHP if (isset($_SESSION["reset_details"]) && isset($_SESSION["reset_details"]["email"]) && !empty($_SESSION["reset_details"]["email"])) { echo $_SESSION["reset_details"]["email"]; } ?>" />
                            
                            <br />
                            <div class="form-error centered" style="margin-bottom: 8px;"><?PHP if (isset($_SESSION["reset_errors"]) && isset($_SESSION["reset_errors"]["captcha"]) && !empty($_SESSION["reset_errors"]["captcha"])) { echo $_SESSION["reset_errors"]["captcha"]; } ?></div>
                            <div class="g-recaptcha" data-sitekey="***REMOVED_G_RECAPTCHA_SITEKEY***" data-theme="light" data-size="normal" ></div>

                            <br />
                            <input type="submit" class="assembl-btn full-width" id="reset-form-submit" name="reset-form-submit" value="Send password reset e-mail" />
                            <div style="font-size: smaller; margin-top: 8px; height: 12px;">
                                <div style="text-align: center; float: left; width: 100%;"><a href="/signin/">Back to sign in</a></div>
                            </div>
                        </form>
                    <?PHP } else if ($_GET["step"] == "mailsent" && isset($_SESSION["pw_reset_mail_sent"]) && $_SESSION["pw_reset_mail_sent"] === true) { ?>
                        <p><b>We've sent you a link to reset your password via your e-mail address.</b></p>
                        <p><small>You can now close this window.</small></p>
                    <?PHP 
                        } else if ($_GET["step"] == "code" && ((isset($_GET["code"]) && !empty($_GET["code"])) || (isset($_SESSION["pw_reset_uid"]) && !empty($_SESSION["pw_reset_uid"])))) {
                            require_once("import/assembldb.php");
                            $connection = AssemblDB::getAccountsConnection();
                            $sql = "SELECT uid, reset_code, reset_code_expires FROM `users`.`accounts` WHERE reset_code='".AssemblDB::makeSafe($_GET["code"], $connection)."' LIMIT 1";
                            $result = mysqli_query($connection, $sql);
                            if (mysqli_num_rows($result) > 0 || (isset($_SESSION["pw_reset_uid"]) && !empty($_SESSION["pw_reset_uid"]))) {
                                $accountData = mysqli_fetch_assoc($result);
                                if (!isset($_SESSION["pw_reset_uid"])) {
                                    $resetCodeExpires = strtotime($accountData["reset_code_expires"]);
                                }
                                else {
                                    $resetCodeExpires = 0;
                                }
                                if ($resetCodeExpires < time() || (isset($_SESSION["pw_reset_uid"]) && !empty($_SESSION["pw_reset_uid"]))) {
                                    if (!isset($_SESSION["pw_reset_uid"])) {
                                        $_SESSION["pw_reset_uid"] = $accountData["uid"];
                                        $sql = "UPDATE `users`.`accounts` SET `reset_code`=NULL, `reset_code_expires`=NULL WHERE uid='".AssemblDB::makeSafe($accountData["uid"], $connection)."' LIMIT 1";
                                        $result = mysqli_query($connection, $sql);
                                    }
                                    ?>
                                        <form action="/callback/pwreset-cb/?step=pwchangecode" method="post" autocomplete="off">
                                            <label for="reset-form-password">New password</label>
                                            <input class="assembl-input" type="password" id="reset-form-password" name="reset-form-password" maxlength="72" />

                                            <label for="reset-form-password-check">Confirm new password</label>
                                            <input class="assembl-input" type="password" id="reset-form-password-check" name="reset-form-password-check" maxlength="72" />
                                        
                                            <br />
                                            <input type="submit" class="assembl-btn full-width" id="reset-form-submit" name="reset-form-submit" value="Reset password" />
                                        </form>
                                    <?PHP
                                }
                                else {
                                    ?>
                                    <p><b>This link has expired.</b></p>
                                    <?PHP echo $resetCodeExpires; ?>
                                    <br />
                                    <?PHP echo time(); ?>
                                    <p><small>Click <a href="/passwordreset/?sendmail">here</a> if you still wish to reset your password.</small></p>
                                <?PHP
                                }
                            }
                            else {
                                ?>
                                    <p><b>This link is invalid or has already been used.</b></p>
                                    <p><small>Click <a href="/passwordreset/?sendmail">here</a> if you still wish to reset your password.</small></p>
                                <?PHP
                            }
                        } else if ($_GET["step"] == "confirm" && $_SESSION["pw_reset_success"] === true) { ?>
                            <p><b>Your password has been reset.</b></p>
                            <p><small>You can now sign in using your new password.</small></p>
                            <button class="assembl-btn full-width" onclick="window.location.href = '/signin/';">Sign in now</button>
                        <?PHP
                            unset($_SESSION["pw_reset_success"]); 
                        } else {
                            $_SESSION["reset_details"] = array();
                            $_SESSION["reset_errors"] = array();
                            $_SESSION["reset_errors"]["general"] = "Something went oddly wrong. Please try again.";
                            header("Location: /passwordreset/");
                            die();
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?PHP 
    $_SESSION["reset_errors"] = array();
?>
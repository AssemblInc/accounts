<?PHP
    require("import/sessionstart.php");
    if (isset($_GET["json"])) {
        $_SESSION["signin_return_key_as_json"] = true;
    }
    else {
        $_SESSION["signin_return_key_as_json"] = false;
    }

    require_once("import/continuer.php");

    if (isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true) {
        if ($urlSpecified) {
            header("Location: ".$continueUrl);
            die();
        }
        else if (!isset($_GET["step"]) || $_GET["step"] != "signed_in") {
            header("Location: /signin/?step=signed_in");
            die();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?PHP echo ((isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true) ? "You are signed in to Assembl" : "Sign in to Assembl"); ?></title>
        <base href="https://accounts.assembl.net/" />
        <link rel="stylesheet" href="/loginstyles.css" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
        <?PHP if (isset($_SESSION["signin_captcha_required"]) && $_SESSION["signin_captcha_required"] === true) { ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
        <?PHP } ?>
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
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <div style="display: none;" id="loading">
                        <img class="loading-svg" src="import/loading.svg" />
                    </div>
                    <script src="/import/loader.js"></script>
                    <h1>Assembl</h1>
                    <?PHP if (!empty($signInReason)) { echo '<div class="reasoning centered">' . $signInReason . '</div>'; } ?>
                    <hr />
                    <?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["general"]) && !empty($_SESSION["signin_errors"]["general"])) { echo '<div class="form-error centered">' . $_SESSION["signin_errors"]["general"] . '</div><hr />'; } ?>
                    <?PHP if (!isset($_GET["step"]) || empty($_GET["step"])) { ?>
                        <form action="/callback/signin-cb/?step=init&continue=<?PHP echo $encodedContinueUrl; ?>" method="post" autocomplete="off">
                            <label for="signin-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["email"]) && !empty($_SESSION["signin_errors"]["email"])) { echo $_SESSION["signin_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" maxlength="100" id="signin-form-email" name="signin-form-email" value="<?PHP if (isset($_SESSION["signin_details"]) && isset($_SESSION["signin_details"]["email"]) && !empty($_SESSION["signin_details"]["email"])) { echo $_SESSION["signin_details"]["email"]; } ?>" onkeyup="document.getElementById('passwordreset').setAttribute('href', '/passwordreset/?continue=<?PHP echo $encodedContinueUrl; ?>&email='+encodeURIComponent(this.value));" />

                            <label for="signin-form-password">Password</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["password"]) && !empty($_SESSION["signin_errors"]["password"])) { echo $_SESSION["signin_errors"]["password"]; } ?></div>
                            <input class="assembl-input" type="password" maxlength="72" id="signin-form-password" name="signin-form-password" />

                            <?PHP if (isset($_SESSION["signin_captcha_required"]) && $_SESSION["signin_captcha_required"] === true) { ?>
                                <br />
                                <div class="form-error centered" style="margin-bottom: 8px;"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["captcha"]) && !empty($_SESSION["signin_errors"]["captcha"])) { echo $_SESSION["signin_errors"]["captcha"]; } ?></div>
                                <noscript><div class="form-error centered">Please disable NoScript to complete a captcha and prove you're not a bot.</div></noscript>
                                <div class="g-recaptcha" data-sitekey="***REMOVED_G_RECAPTCHA_SITEKEY***" data-theme="light" data-size="normal" ></div>
                            <?PHP } ?>

                            <br />
                            <input type="submit" class="assembl-btn full-width" id="signin-form-submit" name="signin-form-submit" value="Sign in" />
                            <div class="below-submit">
                                <div style="float: left; text-align: left;"><a id="passwordreset" href="/passwordreset/?continue=<?PHP echo $encodedContinueUrl; ?>">Forgot password</a></div>
                                <div style="float: right; text-align: right;">No account yet? <a href="/register/?continue=<?PHP echo $encodedContinueUrl; ?>">Register now</a></div>
                            </div>
                        </form>
                    <?PHP } else if ($_GET["step"] == "signed_in" && isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true) { ?>
                        <p><b>Hi <?PHP echo $_SESSION["userdata"]["name"]; ?>!</b></p>
                        <p><small>You are signed in with your Assembl account.</small></p>
                        <p><small><a href="/settings/">Manage your account</a></small></p>
                        <a class="assembl-btn full-width" href="/signout/">Sign out</a>
                    <?PHP } else {
                        $_SESSION["signin_details"] = array();
                        $_SESSION["signin_errors"] = array();
                        $_SESSION["signin_errors"]["general"] = "Something went oddly wrong. Please try again.";
                        header("Location: /signin/?continue=<?PHP echo $encodedContinueUrl; ?>");
                        die();
                    } ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?PHP
    $_SESSION["signin_errors"] = array();
?>

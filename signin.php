<?PHP
    session_start();
    if (isset($_GET["json"])) {
        $_SESSION["signin_return_key_as_json"] = true;
    }
    else {
        $_SESSION["signin_return_key_as_json"] = false;
    }

    if (isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true && (!isset($_GET["step"]) || $_GET["step"] != "signed_in")) {
        header("Location: /signin/?step=signed_in");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Sign in to Assembl</title>
        <base href="https://accounts.assembl.ch/" />
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
        <div id="background-image"></div>
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <div style="display: none;" id="loading">
                        <img class="loading-svg" src="import/loading.svg" />
                    </div>
                    <script src="/import/loader.js"></script>
                    <h1>Assembl</h1>
                    <h2>Sign in</h2>
                    <hr />
                    <?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["general"]) && !empty($_SESSION["signin_errors"]["general"])) { echo '<div class="form-error centered">' . $_SESSION["signin_errors"]["general"] . '</div><hr />'; } ?>
                    <?PHP if (!isset($_GET["step"]) || empty($_GET["step"])) { ?>
                        <form action="/callback/signin-cb/?step=init" method="post" autocomplete="off">
                            <label for="signin-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["email"]) && !empty($_SESSION["signin_errors"]["email"])) { echo $_SESSION["signin_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" id="signin-form-email" name="signin-form-email" value="<?PHP if (isset($_SESSION["signin_details"]) && isset($_SESSION["signin_details"]["email"]) && !empty($_SESSION["signin_details"]["email"])) { echo $_SESSION["signin_details"]["email"]; } ?>" />
                        
                            <label for="signin-form-password">Password</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["password"]) && !empty($_SESSION["signin_errors"]["password"])) { echo $_SESSION["signin_errors"]["password"]; } ?></div>
                            <input class="assembl-input" type="password" id="signin-form-password" name="signin-form-password" maxlength="72" />

                            <?PHP if (isset($_SESSION["signin_captcha_required"]) && $_SESSION["signin_captcha_required"] === true) { ?>
                                <br />
                                <div class="form-error centered" style="margin-bottom: 8px;"><?PHP if (isset($_SESSION["signin_errors"]) && isset($_SESSION["signin_errors"]["captcha"]) && !empty($_SESSION["signin_errors"]["captcha"])) { echo $_SESSION["signin_errors"]["captcha"]; } ?></div>
                                <div class="g-recaptcha" data-sitekey="***REMOVED_G_RECAPTCHA_SITEKEY***" data-theme="light" data-size="normal" ></div>
                            <?PHP } ?>

                            <br />
                            <input type="submit" class="assembl-btn full-width" id="signin-form-submit" name="signin-form-submit" value="Sign in" />
                            <div style="font-size: smaller; margin-top: 8px; height: 12px;">
                                <div style="float: left; text-align: left;"><a href="/passwordreset/">Forgot password</a></div>
                                <div style="float: right; text-align: right;">No account yet? <a href="/register/">Register now</a></div>
                            </div>
                        </form>
                    <?PHP } else if ($_GET["step"] == "signed_in" && isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true) { ?>
                        <p><b>Hi <?PHP echo $_SESSION["userdata"]["name"]; ?>!</b></p>
                        <p><small>You are signed in with your Assembl account.</small></p>
                        <button class="assembl-btn full-width" onclick="window.location.href = '/signout/';">Sign out</button>
                    <?PHP } else {
                        $_SESSION["signin_details"] = array();
                        $_SESSION["signin_errors"] = array();
                        $_SESSION["signin_errors"]["general"] = "Something went oddly wrong. Please try again.";
                        header("Location: /signin/");
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
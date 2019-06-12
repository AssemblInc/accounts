<?PHP
    session_start();

    if (isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] === true) {
        header("Location: /signin/?step=signed_in");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Register for an Assembl account</title>
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

        function seeTerms(e) {
            e = event || e;
            e.preventDefault();
            var w = 500;
            var h = 800;
            var left = (window.innerWidth/2)-(w/2);
            var top = (window.innerHeight/2)-(h/2);
            oAuthWindow = window.open(e.target.getAttribute("href"), "_blank", "toolbar=no, scrollbars=yes, width="+w+", height="+h+", top="+top+", left="+left);
            return true;
        }

        function setMinMaxBirthDate() {
            var date = new Date();
            var dd = date.getDate();
            var mm = date.getMonth()+1;
            var yyyy = date.getFullYear();
            if(dd<10){
                dd='0'+dd;
            } 
            if(mm<10){
                mm='0'+mm;
            } 

            var today = yyyy+'-'+mm+'-'+dd;
            var longago = (yyyy - 150)+'-'+mm+'-'+dd;
            document.getElementById("register-form-birth-date").setAttribute("max", today);
            document.getElementById("register-form-birth-date").setAttribute("min", longago);
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
                    <?PHP if (!isset($_GET["step"]) || intval($_GET["step"]) == 1) { ?>
                        <h2>Register for an account</h2>
                        <hr />
                        <form action="/callback/register-cb/?step=1" method="post" autocomplete="off">
                            <?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["general"]) && !empty($_SESSION["register_errors"]["general"])) { echo '<div class="form-error centered">' . $_SESSION["register_errors"]["general"] . '</div><hr />'; } ?>
                            <label for="register-form-name">Name</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["name"]) && !empty($_SESSION["register_errors"]["name"])) { echo $_SESSION["register_errors"]["name"]; } ?></div>
                            <input class="assembl-input" type="text" id="register-form-name" name="register-form-name" value="<?PHP if (isset($_SESSION["register_details"]) && isset($_SESSION["register_details"]["name"]) && !empty($_SESSION["register_details"]["name"])) { echo $_SESSION["register_details"]["name"]; } ?>" />

                            <label for="register-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["email"]) && !empty($_SESSION["register_errors"]["email"])) { echo $_SESSION["register_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" id="register-form-email" name="register-form-email" value="<?PHP if (isset($_SESSION["register_details"]) && isset($_SESSION["register_details"]["email"]) && !empty($_SESSION["register_details"]["email"])) { echo $_SESSION["register_details"]["email"]; } ?>" placeholder="example@domain.com" />
                            
                            <label for="register-form-birth-date">Birth date</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["birth-date"]) && !empty($_SESSION["register_errors"]["birth-date"])) { echo $_SESSION["register_errors"]["birth-date"]; } ?></div>
                            <input class="assembl-input" type="date" id="register-form-birth-date" name="register-form-birth-date" value="<?PHP if (isset($_SESSION["register_details"]) && isset($_SESSION["register_details"]["birth-date"]) && !empty($_SESSION["register_details"]["birth-date"])) { echo $_SESSION["register_details"]["birth-date"]; } ?>" placeholder="YYYY-MM-DD" />

                            <label for="register-form-password">Password</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["password"]) && !empty($_SESSION["register_errors"]["password"])) { echo $_SESSION["register_errors"]["password"]; } ?></div>
                            <input class="assembl-input" type="password" id="register-form-password" name="register-form-password" maxlength="72" />

                            <label for="register-form-password-check">Confirm password</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["password-check"]) && !empty($_SESSION["register_errors"]["password-check"])) { echo $_SESSION["register_errors"]["password-check"]; } ?></div>
                            <input class="assembl-input" type="password" id="register-form-password-check" name="register-form-password-check" maxlength="72" />

                            <br />
                            <div style="text-align: center;">
                                <div class="form-error centered"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["terms"]) && !empty($_SESSION["register_errors"]["terms"])) { echo $_SESSION["register_errors"]["terms"]; } ?></div>
                                <input type="checkbox" id="register-form-terms" name="register-form-terms" value="true" <?PHP if (isset($_SESSION["register_details"]) && isset($_SESSION["register_details"]["terms"]) && $_SESSION["register_details"]["terms"] === true) { echo "checked "; } ?>/><label class="forcheckbox" for="register-form-terms">I agree to the <a href="https://assembl.ch/terms/" target="_blank" onclick="seeTerms(event);">terms &amp; conditions</a></label>
                            </div>

                            <br />
                            <div class="form-error centered" style="margin-bottom: 8px;"><?PHP if (isset($_SESSION["register_errors"]) && isset($_SESSION["register_errors"]["captcha"]) && !empty($_SESSION["register_errors"]["captcha"])) { echo $_SESSION["register_errors"]["captcha"]; } ?></div>
                            <div class="g-recaptcha" data-sitekey="***REMOVED_G_RECAPTCHA_SITEKEY***" data-theme="light" data-size="normal" ></div>

                            <br />
                            <input type="submit" class="assembl-btn full-width" id="register-form-submit" name="register-form-submit" value="Register" />
                            <div style="font-size: smaller; margin-top: 8px; height: 12px;">
                                <div style="text-align: center; float: left; width: 100%;">Already have an account? <a href="/signin/">Sign in</a></div>
                            </div>
                        </form>
                        <script> setMinMaxBirthDate(); </script>
                    <?PHP } else if (intval($_GET["step"]) == 2 && isset($_SESSION["email_verif_code"]) && !empty($_SESSION["email_verif_code"])) { ?>
                        <h2>Confirm your e-mail</h2>
                        <hr />
                        <form action="/callback/register-cb/?step=2" method="post" autocomplete="off">
                            <p><b>We've sent you an e-mail containing a verification code.</b></p>
                            <p><small>Copy the code and enter it here. Do not close this window.</small></p>

                            <label for="verification-form-code">Code</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["verification_errors"]) && isset($_SESSION["verification_errors"]["code"]) && !empty($_SESSION["verification_errors"]["code"])) { echo $_SESSION["verification_errors"]["code"]; } ?></div>
                            <input class="assembl-input" type="number" id="verification-form-code" name="verification-form-code" />
                            
                            <br />
                            <input type="submit" class="assembl-btn full-width" id="verification-form-submit" name="verification-form-submit" value="Proceed" />
                        </form>
                    <?PHP } else if (intval($_GET["step"]) == 3 && isset($_SESSION["account_created"]) && $_SESSION["account_created"] === true) { ?>
                        <h2>All done!</h2>
                        <hr />
                        <p><b>Your account has been set up.</b></p>
                        <p><small>You can now sign in.</small></p>
                        <button class="assembl-btn full-width" onclick="window.location.href = '/signin/';">Sign in now</button>
                    <?PHP 
                            unset($_SESSION["account_created"]);
                        } else {
                        $_SESSION["register_details"] = array();
                        $_SESSION["register_errors"] = array();
                        $_SESSION["register_errors"]["general"] = "Something went oddly wrong. Please try again.";
                        $_SESSION["verification_errors"] = array();
                        $_SESSION["verification_incorrect_attempts"] = 0;
                        header("Location: /register/?step=1");
                        die();
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?PHP 
    $_SESSION["register_errors"] = array();
?>
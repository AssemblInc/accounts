<?PHP
    require("import/sessionstart.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Delete your Assembl account</title>
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
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <div style="display: none;" id="loading">
                        <img class="loading-svg" src="import/loading.svg" />
                    </div>
                    <script src="/import/loader.js"></script>
                    <h1>Assembl</h1>
                    <h2>Delete your account</h2>
                    <hr />
                    <form action="/callback/delete-cb/" method="post" autocomplete="off">
                        <label for="delete-form-confirm">Write DELETE below in all capital letters to confirm</label>
                        <div class="form-error"><?PHP if (isset($_SESSION["delete_errors"]) && isset($_SESSION["delete_errors"]["confirm"]) && !empty($_SESSION["delete_errors"]["confirm"])) { echo $_SESSION["delete_errors"]["confirm"]; } ?></div>
                        <input class="assembl-input" type="text" maxlength="24" id="delete-form-confirm" name="delete-form-confirm" />
                        
                        <br />
                        <div style="text-align: center; white-space: nowrap;">
                            <div class="form-error centered"><?PHP if (isset($_SESSION["delete_errors"]) && isset($_SESSION["delete_errors"]["understand"]) && !empty($_SESSION["delete_errors"]["understand"])) { echo $_SESSION["delete_errors"]["understand"]; } ?></div>
                            <input type="checkbox" id="delete-form-understand" name="delete-form-understand" value="true" /><label class="forcheckbox" style="font-size: small;" for="delete-form-understand">I agree that I understand that, by filling in this form and clicking the button below, my account will get deleted permanently, and that this action is irreversible.</label>
                        </div>

                        <br />
                        <div class="form-error centered" style="margin-bottom: 8px;"><?PHP if (isset($_SESSION["delete_errors"]) && isset($_SESSION["delete_errors"]["captcha"]) && !empty($_SESSION["delete_errors"]["captcha"])) { echo $_SESSION["delete_errors"]["captcha"]; } ?></div>
                        <noscript><div class="form-error centered">Please disable NoScript to complete a captcha and prove you're not a bot.</div></noscript>
                        <div class="g-recaptcha" data-sitekey="***REMOVED_G_RECAPTCHA_SITEKEY***" data-theme="light" data-size="normal" ></div>

                        <br />
                        <input type="submit" class="assembl-btn full-width bad-btn" id="delete-form-submit" name="delete-form-submit" value="Delete my account" />
                        <div class="below-submit">
                            <div style="text-align: center; float: left; width: 100%;"><a href="/settings/">Back to settings</a></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
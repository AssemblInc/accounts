<?PHP
    require("import/sessionstart.php");
    require_once("import/continuer.php");

    if (!isset($_SESSION["signed_in"])) {
        header("Location: /signin/?continue=".$encodedContinueUrl);
        die();
    }

    session_unset();
    session_destroy();
    session_write_close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>You have been signed out</title>
        <base href="https://accounts.assembl.net/" />
        <link rel="stylesheet" href="/loginstyles.css" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
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
                    <hr />
                    <?PHP if (!isset($_GET["del"])) { ?>
                        <p><small>You have been signed out from your Assembl account.</small></p>
                        <a class="assembl-btn full-width" href="/signin/?continue=<?PHP echo $encodedContinueUrl; ?>">Sign in with a different account</a>
                    <?PHP } else { ?>
                        <p><small>Your account has been deleted and you have been signed out.</small></p>
                        <a class="assembl-btn full-width" href="/">Continue</a>
                    <?PHP } ?>
                </div>
            </div>
        </div>
    </body>
</html>

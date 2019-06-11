<?PHP
    session_start();
    if (!isset($_SESSION["signed_in"])) {
        header("Location: /signin/");
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
        <img src="https://assembl.ch/images/bg.jpg" id="background-image" />
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <h1>Assembl</h1>
                    <h2>You have been signed out</h2>
                    <hr />
                    <p><b>You have been signed out from your Assembl account.</b></p>
                    <button class="assembl-btn full-width" onclick="window.location.href = '/signin/';">Sign in with a different account</button>
                </div>
            </div>
        </div>
    </body>
</html>
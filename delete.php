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
                    <p>Deleting an Assembl account is not possible yet. We're still working on this functionality. Please come back later!</p>
                    <p><small>If you really need to have your account deleted right now, please send us an e-mail at <a href="mailto:contact@assembl.us">contact@assembl.us</a> and we will help you out.</small></p>
                    <a class="assembl-btn full-width" href="/settings/">Go back to settings</a>
                </div>
            </div>
        </div>
    </body>
</html>
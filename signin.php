<?PHP
    session_start();
    if (isset($_GET["json"])) {
        $_SESSION["signin_return_key_as_json"] = true;
    }
    else {
        $_SESSION["signin_return_key_as_json"] = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Sign in to Assembl</title>
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
        <img src="https://assembl.science/images/bg.jpg" id="background-image" />
        <div class="signin-table">
            <div class="signin-table-cell">
                <div class="signin-table-cell-content">
                    <h1>Assembl</h1>
                    <h2>Sign in to Assembl</h2>
                    <hr />
                    <p><b>Sign in with your ORCID iD to continue to Assembl.</b></p>
                    <p><small>ORCID provides a persistent identifier &ndash; an ORCID iD &ndash; that distinguishes you from other researchers and a mechanism for linking your research outputs and activities to your iD. Learn more at <a target="_blank" href="https://orcid.org">orcid.org</a>.</small></p>
                    <button id="orcid-btn" style="width: 100%;"><img id="orcid-id-icon" src="https://orcid.org/sites/default/files/images/orcid_24x24.png" width="24" height="24" alt="ORCID iD icon"/><span>Register or Connect your ORCID iD</span></button>
                    <script>
                    document.getElementById("orcid-btn").addEventListener("click", function() {
                        var signInUrl = "https://orcid.org/oauth/authorize?client_id=***REMOVED_ORCID_CLIENT_ID***&response_type=code&scope=/authenticate";
                        if (getParameterByName("orcid") != null) {
                            signInUrl += "&orcid="+getParameterByName("orcid");
                        }
                        signInUrl += "&redirect_uri=https://accounts.assembl.science/callback/orcid/";
                        window.location.href = signInUrl;
                    });
                    </script>
                </div>
            </div>
        </div>
    </body>
</html>
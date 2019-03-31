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
        <title>Sign in to Assembl</title>
        <link rel="stylesheet" href="https://assembl.science/import/css/simple.css" />
    </head>
    <body>
        <h1><a href="https://assembl.science/">Assembl</a></h1>
        <h2>Sign in to Assembl</h2>
        <hr />
        <p><b>Sign in with your ORCID iD to continue to Assembl.</b></p>
        <p><small>ORCID provides a persistent identifier &ndash; an ORCID iD &ndash; that distinguishes you from other researchers and a mechanism for linking your research outputs and activities to your iD. Learn more at <a href="https://orcid.org">orcid.org</a>.</small></p>
        <button id="orcid-btn"><img id="orcid-id-icon" src="https://orcid.org/sites/default/files/images/orcid_24x24.png" width="24" height="24" alt="ORCID iD icon"/>Register or Connect your ORCID iD</button>
        <script>
        var oAuthWindow;
        document.getElementById("orcid-btn").addEventListener("click", function() {
            var w = 500;
            var h = 600;
            var left = (window.innerWidth/2)-(w/2);
            var top = (window.innerHeight/2)-(h/2);
            oAuthWindow = window.open("https://orcid.org/oauth/authorize?client_id=***REMOVED_ORCID_CLIENT_ID***&response_type=code&scope=/authenticate&redirect_uri=https://accounts.assembl.science/callback/orcid/", "_blank", "toolbar=no, scrollbars=yes, width="+w+", height="+h+", top="+top+", left="+left);
        });
        </script>
    </body>
</html>
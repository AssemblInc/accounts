<?PHP
    require("import/sessionstart.php");
    require("api/requirelogin.php");

    if (!isset($_GET["s"]) || empty($_GET["s"])) {
        header("Location: /settings/");
        die();
    }

    switch($_GET["s"]) {
        case "orcid":
            $s = "orcid";
            $sName = "ORCID";
            $sExp = 'ORCID provides a persistent identifier – an ORCID iD – that distinguishes you from other researchers and a mechanism for linking your research outputs and activities to your iD. Learn more at <a target="_blank" href="https://orcid.org">orcid.org</a>.';
            $sBtn = '<img src="https://orcid.org/sites/default/files/images/orcid_24x24.png" width="24" height="24" alt="ORCID iD icon" style="vertical-align: middle;"><span style="vertical-align: middle; line-height: 24px;">Connect your ORCID iD</span>';
            $sUrl = "https://orcid.org/oauth/authorize?client_id=***REMOVED_ORCID_CLIENT_ID***&response_type=code&scope=/authenticate&redirect_uri=https://accounts.assembl.net/callback/connect-cb/?s=orcid";
            break;
        default:
            header("Location: /settings/");
            die();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Connect <?PHP echo $sName; ?> to your Assembl account</title>
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
                    <h2>Connect <?PHP echo $sName; ?> to your Assembl account</h2>
                    <hr />
                    <p><small><?PHP echo $sExp; ?></small></p>
                    <a class="assembl-btn full-width no-height-limit" href="<?PHP echo $sUrl; ?>"><?PHP echo $sBtn; ?></a>
                </div>
            </div>
        </div>
    </body>
</html>

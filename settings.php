<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);
    require("import/sessionstart.php");
    require_once("api/requirelogin.php");

    function encodeURIComponent($str) {
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode($str), $revert);
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Assembl Account Settings</title>
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
            document.getElementById("settings-form-birth-date").setAttribute("max", today);
            document.getElementById("settings-form-birth-date").setAttribute("min", longago);
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
                    <h2>Account Settings</h2>
                    <hr />
                    <form action="/callback/settings-cb/" method="post" autocomplete="off">
                        <fieldset>
                            <legend>Your Details</legend>

                            <label for="settings-form-name">Name</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["name"]) && !empty($_SESSION["settings_errors"]["name"])) { echo $_SESSION["settings_errors"]["name"]; } ?></div>
                            <input class="assembl-input" type="text" maxlength="64" id="settings-form-name" name="settings-form-name" value="<?PHP echo $_SESSION["userdata"]["name"]; ?>" />

                            <label for="settings-form-birth-date">Birth date</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["birth-date"]) && !empty($_SESSION["settings_errors"]["birth-date"])) { echo $_SESSION["settings_errors"]["birth-date"]; } ?></div>
                            <input class="assembl-input" type="date" id="settings-form-birth-date" name="settings-form-birth-date" value="<?PHP echo $_SESSION["userdata"]["birthdate"]; ?>" placeholder="YYYY-MM-DD" />
                            <script> setMinMaxBirthDate(); </script>

                            <label for="settings-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["email"]) && !empty($_SESSION["settings_errors"]["email"])) { echo $_SESSION["settings_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" maxlength="100" id="settings-form-email" name="settings-form-email" value="<?PHP echo $_SESSION["userdata"]["email_address"]; ?>" placeholder="example@domain.com" />

                            <label for="settings-form-org-affiliation">Position &amp; Organization</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["org-affiliation"]) && !empty($_SESSION["settings_errors"]["org-affiliation"])) { echo $_SESSION["settings_errors"]["org-affiliation"]; } ?></div>
                            <input class="assembl-input" type="text" maxlength="100" id="settings-form-org-affiliation" name="settings-form-org-affiliation" value="<?PHP echo $_SESSION["userdata"]["org_affiliation"]; ?>" placeholder="Researcher at Random Institute" />
                        </fieldset>
                        <fieldset>
                            <legend>Connections</legend>

                            <label for="settings-form-orcid">ORCID iD</label>
                            <input class="assembl-input" type="text" disabled value="UNDER CONSTRUCTION" />

                        </fieldset>
                        <fieldset>
                            <legend>Security</legend>

                            <a href="/passwordreset/?email=<?PHP echo encodeURIComponent($_SESSION["userdata"]["email_address"]); ?>&continue=https%3A%2F%2Faccounts.assembl.ch%2Fsettings%2F">Change your password</a>
                            
                            <br />

                            <a href="/history/?of=logins">View login history</a>

                        </fieldset>
                        <fieldset>
                            <legend>Delete account</legend>

                            <a href="/delete/">Delete your account</a>

                        </fieldset>

                        <br />
                        <input type="submit" class="assembl-btn full-width" id="settings-form-submit" name="settings-form-submit" value="Save" />
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
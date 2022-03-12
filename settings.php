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
                            <input class="assembl-input" type="date" id="settings-form-birth-date" name="settings-form-birth-date" value="<?PHP echo $_SESSION["userdata"]["birth_date"]; ?>" placeholder="YYYY-MM-DD" />
                            <script> setMinMaxBirthDate(); </script>

                            <label for="settings-form-email">E-mail address</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["email"]) && !empty($_SESSION["settings_errors"]["email"])) { echo $_SESSION["settings_errors"]["email"]; } ?></div>
                            <input class="assembl-input" type="email" maxlength="100" id="settings-form-email" name="settings-form-email" value="<?PHP echo $_SESSION["userdata"]["email_address"]; ?>" placeholder="example@domain.com" />

                            <label for="settings-form-org-affiliation">Position &amp; Organization</label>
                            <div class="form-error"><?PHP if (isset($_SESSION["settings_errors"]) && isset($_SESSION["settings_errors"]["org-affiliation"]) && !empty($_SESSION["settings_errors"]["org-affiliation"])) { echo $_SESSION["settings_errors"]["org-affiliation"]; } ?></div>
                            <input class="assembl-input" type="text" maxlength="100" id="settings-form-org-affiliation" name="settings-form-org-affiliation" value="<?PHP echo $_SESSION["userdata"]["org_affiliation"]; ?>" placeholder="e.g. Researcher at Random Institute" />
                        </fieldset>
                        <fieldset>
                            <legend>Connections</legend>

                            <label for="settings-form-orcid">ORCID iD</label>
                            <?PHP
                                require("import/assembldb.php");
                                $connection = AssemblDB::getAccountsConnection();
                                $sql = "SELECT * FROM `users`.`orcid` WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                                $result = mysqli_query($connection, $sql);
                                $orcidData = mysqli_fetch_assoc($result);
                                if (!empty($orcidData["orcid_id"])) {
                                    ?>
                                        <input class="assembl-input" readonly type="text" value="<?PHP echo $orcidData["orcid_id"]; ?>" />
                                        <small><a href="/disconnect/?s=orcid">Disconnect</a></small>
                                    <?PHP
                                }
                                else {
                                    ?>
                                        <a href="/connect/?s=orcid">Connect your ORCID iD</a>
                                    <?PHP
                                }
                            ?>
                        </fieldset>
                        <fieldset>
                            <legend>Security</legend>

                            <a href="/passwordreset/?email=<?PHP echo encodeURIComponent($_SESSION["userdata"]["email_address"]); ?>&continue=https%3A%2F%2Faccounts.assembl.net%2Fsettings%2F">Change your password</a>

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
        <script>
        function confirmOnPageExit(e) {
            e = e || window.event;
            var message = "Changes you made may not be saved.";
            if (e) {
                e.returnValue = message;
            }
            return message;
        }

        function enableExitConfirmation() {
            console.log("Exit Confirmation enabled");
            window.onbeforeunload = confirmOnPageExit;
        }

        function disableExitConfirmation() {
            console.log("Exit Confirmation disabled");
            window.onbeforeunload = null;
        }

        function setUpPageExitConfirmation() {
            var elems = [];
            elems = elems.concat([].slice.call(document.getElementsByTagName("input")));
            elems = elems.concat([].slice.call(document.getElementsByTagName("textarea")));
            elems = elems.concat([].slice.call(document.getElementsByTagName("select")));
            elems = elems.concat([].slice.call(document.getElementsByTagName("form")));
            for (var i = 0; i < elems.length; i++) {
                switch(elems[i].tagName.toLowerCase()) {
                    case "input":
                    case "textarea":
                        if (elems[i].getAttribute("type") != "file") {
                            elems[i].addEventListener("input", enableExitConfirmation);
                        }
                        else {
                            elems[i].addEventListener("change", enableExitConfirmation);
                        }
                        break;
                    case "select":
                        elems[i].addEventListener("change", enableExitConfirmation);
                        break;
                    case "form":
                        elems[i].addEventListener("change", disableExitConfirmation);
                        break;
                }
            }
        }

        setUpPageExitConfirmation();
        </script>
    </body>
</html>

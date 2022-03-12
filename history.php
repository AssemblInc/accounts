<?PHP
    require("import/sessionstart.php");
    require("api/requirelogin.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Account History</title>
        <base href="https://accounts.assembl.net/" />
        <link rel="stylesheet" href="/loginstyles.css" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
        <script src="import/bowser.min.js"></script>
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
                    <h2>Account History</h2>
                    <hr />
                    <?PHP if (!isset($_GET["of"]) || $_GET["of"] == "logins") { ?>
                        <p style="font-size: larger;"><b>Your Login History</b></p>
                        <table class="details-table">
                            <?PHP
                                require_once("import/assembldb.php");
                                $connection = AssemblDB::getLoginsConnection();
                                $sql = "SELECT * FROM `users`.`logins` WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' ORDER BY login_id DESC";
                                $result = mysqli_query($connection, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    $logins = array();
                                    while($row = mysqli_fetch_assoc($result)) {
                                        array_push($logins, $row);
                                    }

                                    foreach ($logins as $login) {
                                        $sql = "SELECT * FROM `user_agents`.`ua` WHERE `ua_key`='".AssemblDB::makeSafe($login["user_agent_key"], $connection)."' LIMIT 1";
                                        $result = mysqli_query($connection, $sql);
                                        $userAgent = mysqli_fetch_assoc($result);
                                        ?>
                                        <tr class="top-row">
                                            <td rowspan="3" class="image">
                                                <img class="login-device-img" data-devimgloginid="<?PHP echo intval($login["login_id"]); ?>" src="import/icons/device-unknown.svg" />
                                            </td>
                                            <td>
                                                <b><?PHP echo date("l, \\t\\h\\e jS \\of F \\a\\t g:i A", strtotime($login["timestamp"])); ?> (UTC)</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="login-user-agent-str login-<?PHP echo intval($login["login_id"]); ?>" data-loginid="<?PHP echo intval($login["login_id"]); ?>"><?PHP echo htmlspecialchars($userAgent["val"]); ?><td>
                                        </tr>
                                        <tr>
                                            <td><span style="font-family: 'Consolas', 'Courier New', Courier, monospace, Serif;"><?PHP echo htmlspecialchars($login["ip_address"]); ?></span> <small><a href="https://www.iplocation.net/?query=<?PHP echo $login["ip_address"]; ?>" target="_blank">view location on iplocation.net</sup></a></small><td>
                                        </tr>
                                        <?PHP
                                    }
                                }
                                else {
                                    ?>
                                        <tr><td><small><i>There are no logins known to us</i></small></td></tr>
                                    <?PHP
                                }
                            ?>
                        </table>
                        <script>
                            function fixUserAgents() {
                                var loginUAs = document.getElementsByClassName("login-user-agent-str");
                                for (var i = 0; i < loginUAs.length; i++) {
                                    var userAgent = loginUAs[i].innerText;
                                    var details = bowser.getParser(userAgent).parsedResult;

                                    // replace user agent with actual readable information
                                    var fixedInfo = details.browser.name;
                                    if (details.browser.version != undefined && details.browser.version != null) {
                                        fixedInfo += " " + details.browser.version;
                                    }
                                    if (details.os != undefined) {
                                        fixedInfo += " <small>on</small> " + details.os.name;
                                        if (details.os.versionName != undefined && details.os.versionName != null) {
                                            fixedInfo += " " + details.os.versionName;
                                        }
                                        else if (details.os.version != undefined && details.os.version != null) {
                                            fixedInfo += " " + details.os.version;
                                        }
                                    }
                                    loginUAs[i].innerHTML = fixedInfo;

                                    // set the corresponding icon
                                    var icon = document.querySelector("[data-devimgloginid='"+loginUAs[i].getAttribute('data-loginid')+"']");
                                    switch (details.platform.type) {
                                        case "desktop": {
                                            switch (details.os.name.toLowerCase()) {
                                                case "macos":
                                                    icon.setAttribute("src", "import/icons/desktop-mac.svg");
                                                    break;
                                                case "windows":
                                                default:
                                                    icon.setAttribute("src", "import/icons/desktop.svg");
                                                    break;
                                            }
                                            break;
                                        }
                                        case "mobile": {
                                            switch (details.os.name.toLowerCase()) {
                                                case "ios":
                                                    icon.setAttribute("src", "import/icons/phone-ios.svg");
                                                    break;
                                                case "android":
                                                    icon.setAttribute("src", "import/icons/phone-android.svg");
                                                    break;
                                                default:
                                                    icon.setAttribute("src", "import/icons/phone.svg");
                                                    break;
                                            }
                                            break;
                                        }
                                        case "tablet": {
                                            switch (details.os.name.toLowerCase()) {
                                                case "ios":
                                                    icon.setAttribute("src", "import/icons/tablet-ios.svg");
                                                    break;
                                                case "android":
                                                    icon.setAttribute("src", "import/icons/tablet-android.svg");
                                                    break;
                                                default:
                                                    icon.setAttribute("src", "import/icons/tablet.svg");
                                                    break;
                                            }
                                            break;
                                        }
                                        case "tv": {
                                            icon.setAttribute("src", "import/icons/tv.svg");
                                            break;
                                        }
                                        default: {
                                            icon.setAttribute("src", "import/icons/device-unknown.svg");
                                            break;
                                        }
                                    }
                                }
                            }
                            fixUserAgents();
                        </script>
                    <?PHP } else {
                        header("Location: /settings/");
                        die();
                    } ?>
                </div>
            </div>
        </div>
    </body>
</html>

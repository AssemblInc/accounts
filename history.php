<?PHP
    require("import/sessionstart.php");
    require("api/requirelogin.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Account History</title>
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
                    <h2>Account History</h2>
                    <hr />
                    <?PHP if (!isset($_GET["of"]) || $_GET["of"] == "logins") { ?>
                        <p style="font-size: bigger;"><b>Your Login History</b></p>
                        <ul style="list-style-type: none; padding: 0px;">
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
                                        <li style="margin: 24px 0px; font-size: small;">
                                            <b><?PHP echo date("l jS \\of F \\a\\t g:i A", strtotime($login["timestamp"])); ?> (UTC)</b><br />
                                            <i><?PHP echo $userAgent["val"]; ?></i><br />
                                            <span style="font-family: 'Consolas', 'Courier New', Courier, monospace, Serif;"><?PHP echo $login["ip_address"]; ?></span>
                                        </li>
                                        <?PHP
                                    }
                                }
                                else {
                                    ?>
                                        <li><small><i>There are no logins known to us</i></small></li>
                                    <?PHP
                                }
                            ?>
                        </ul>
                    <?PHP } else { 
                        header("LOcation: /settings/");
                        die();
                    } ?>
                </div>
            </div>
        </div>
    </body>
</html>
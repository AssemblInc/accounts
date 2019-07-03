<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);
    
    require("import/sessionstart.php");
    require("api/requirelogin.php");

    if (!isset($_GET["s"]) || empty($_GET["s"])) {
        header("Location: /settings/");
        die();
    }

    require_once("import/assembldb.php");
    $connection = AssemblDB::getAccountsConnection();

    switch($_GET["s"]) {
        case "orcid":
            $sql = "SELECT `orcid_access_token` FROM `users`.`orcid` WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
            $result = mysqli_query($connection, $sql);
            $accessToken = mysqli_fetch_assoc($result)["orcid_access_token"];
            if (!empty($accessToken)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://orcid.org/oauth/revoke");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                    'client_id' => '***REMOVED_ORCID_CLIENT_ID***',
                    'client_secret' => '***REMOVED_ORCID_CLIENT_SECRET***',
                    'token' => $accessToken
                )));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                
                if ($response !== false) {
                    $sql = "UPDATE `users`.`orcid` SET `orcid_id`=NULL, `orcid_token_type`=NULL, `orcid_access_token`=NULL, `orcid_refresh_token`=NULL, `orcid_expires_on`=NULL, `orcid_scope`=NULL, `connected_on`=NULL WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                    $result = mysqli_query($connection, $sql);

                    if ($result !== false) {
                        if (mysqli_affected_rows($connection) > 0) {
                            header("Location: /settings/");
                            die();
                        }
                        else {
                            die('Could not remove connection details from database. <a href="/disconnect/?s=orcid">Try again</a>');
                        }
                    }
                    else {
                        die('SQL error. <a href="/disconnect/?s=orcid">Try again</a>');
                    }
                }
                else {
                    die('Could not revoke ORCID access token. <a href="/disconnect/?s=orcid">Try again</a>');
                }
            }
            else {
                die('No ORCID iD has been linked to your account. Nothing to disconnect. <a href="/settings/">Go back</a>');
            }
            break;
        default:
            die('Unknown service. <a href="/settings/">Go back</a>');
            break;
    }
?>
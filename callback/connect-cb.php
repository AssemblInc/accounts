<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once("../import/sessionstart.php");

    if (!isset($_GET["s"]) || empty($_GET["s"])) {
        die("Provide the service to connect to with the parameter s in the URL.");
    }
    else {
        switch($_GET["s"]) {
            case "orcid": {
                if (isset($_GET["code"]) && !empty($_GET["code"])) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://orcid.org/oauth/token");
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                        'client_id' => '***REMOVED_ORCID_CLIENT_ID***',
                        'client_secret' => '***REMOVED_ORCID_CLIENT_SECRET***',
                        'grant_type' => 'authorization_code',
                        'code' => $_GET["code"],
                        'redirect_uri' => 'https://accounts.assembl.ch/callback/connect-cb/?s=orcid'
                    )));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    if ($response !== false) {
                        $jsonResponse = json_decode($response, true);
                        if (!array_key_exists("error", $jsonResponse)) {
                            $expiresOn = date("Y-m-d H:i:s", time() + intval($jsonResponse["expires_in"]));

                            require_once("../import/assembldb.php");
                            $connection = AssemblDB::getAccountsConnection();
                            
                            $sql = "UPDATE `users`.`orcid` SET `orcid_id`='".AssemblDB::makeSafe($jsonResponse["orcid"], $connection)."', `orcid_token_type`='".AssemblDB::makeSafe($jsonResponse["token_type"], $connection)."', `orcid_access_token`='".AssemblDB::makeSafe($jsonResponse["access_token"], $connection)."', `orcid_refresh_token`='".AssemblDB::makeSafe($jsonResponse["refresh_token"], $connection)."', `orcid_expires_on`='".AssemblDB::makeSafe($expiresOn, $connection)."', `orcid_scope`='".AssemblDB::makeSafe($jsonResponse["scope"], $connection)."', `connected_on`=CURRENT_TIMESTAMP() WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                            $result = mysqli_query($connection, $sql);
                            
                            if ($result !== false) {
                                if (mysqli_affected_rows($connection) > 0) {
                                    header("Location: /settings/");
                                    die();
                                }
                                else {
                                    die("Could not add connection details to database");
                                }
                            }
                            else {
                                die("SQL error");
                            }
                        }
                        else {
                            die($jsonResponse["error_description"]);
                        }
                    }
                    else {
                        die("Could not trade authorization code for access token");
                    }
                }
                else if (isset($_GET["error"])) {
                    die($_GET["error_description"]);
                }
                else {
                    die("GET parameter 'code' but also 'error' is not set");
                }
                break;
            }
            default: {
                die("Unknown service");
            }
        }
    }
?>
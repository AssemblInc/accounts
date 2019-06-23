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
            $sql = "UPDATE `users`.`orcid` SET `orcid_id`=NULL, `orcid_token_type`=NULL, `orcid_access_token`=NULL, `orcid_refresh_token`=NULL, `orcid_expires_on`=NULL, `orcid_scope`=NULL, `connected_on`=NULL WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
            $result = mysqli_query($connection, $sql);

            if ($result !== false) {
                if (mysqli_affected_rows($connection) > 0) {
                    header("Location: /settings/");
                    die();
                }
                else {
                    die("Could not remove connection details from database");
                }
            }
            else {
                die("SQL error");
            }
            break;
        default:
            die("Unknown service");
            break;
    }
?>
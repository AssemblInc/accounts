<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);
    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);

    if (!isset($_GET["id"]) || empty($_GET["id"])) {
        require("../import/sessionstart.php");

        if (!isset($_SESSION["signed_in"]) || $_SESSION["signed_in"] !== true) {
            header("Location: /signin/?continue=https://accounts.assembl.net/api/account/");
            die();
        }
        else {
            header("Location: /api/account/?id=".$_SESSION["userdata"]["uid"]);
            die();
        }
    }
    else {
        require("../import/assembldb.php");
        $connection = AssemblDB::getAccountsConnection();

        $sql = "SELECT * FROM `users`.`userdata` WHERE `uid`='".AssemblDB::makeSafe($_GET["id"], $connection)."' LIMIT 1";
        $result = mysqli_query($connection, $sql);
        $userData = mysqli_fetch_assoc($result);

        $sql = "SELECT * FROM `users`.`orcid` WHERE `uid`='".AssemblDB::makeSafe($_GET["id"], $connection)."' LIMIT 1";
        $result = mysqli_query($connection, $sql);
        $orcidData = mysqli_fetch_assoc($result);

        $returnData = array();
        $returnData["assembl_id"] = $userData["uid"];
        $returnData["name"] = $userData["name"];
        $returnData["org_affiliation"] = $userData["org_affiliation"];
        $returnData["organization_id"] = $userData["organization_id"];
        $returnData["orcid_id"] = $orcidData["orcid_id"];

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }
?>

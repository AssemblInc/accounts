<?PHP
    // error_reporting(E_ALL); ini_set('display_errors', 1);
    header('Content-Type: application/json; charset=utf-8');
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
    
    require("../import/sessionstart.php");

    if (!isset($_SESSION["signed_in"]) || $_SESSION["signed_in"] !== true) {
        header("Location: /signin/?continue=https://accounts.assembl.ch/api/tempapp/");
        die();
    }
    else {
        require("../import/assembldb.php");
        $connection = AssemblDB::getAccountsConnection();

        $sql = "SELECT * FROM `users`.`orcid` WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
        $result = mysqli_query($connection, $sql);
        $orcidData = mysqli_fetch_assoc($result);
        
        $returnData = array();
        $returnData["assembl_id"] = $_SESSION["userdata"]["uid"];
        $returnData["orcid_id"] = $orcidData["orcid_id"];
        $returnData["token_type"] = $orcidData["orcid_token_type"];
        $returnData["access_token"] = $orcidData["orcid_access_token"];
        $returnData["refresh_token"] = $orcidData["orcid_refresh_token"];
        $returnData["expires_in"] = strtotime($orcidData["orcid_expires_on"]);
        $returnData["orcid_scope"] = $orcidData["orcid_scope"];
        $returnData["name"] = $_SESSION["userdata"]["name"];

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }
?>
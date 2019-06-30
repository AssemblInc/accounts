<?PHP
    require("import/sessionstart.php");

    if (!isset($_GET["id"]) || empty($_GET["id"])) {
        if (!isset($_SESSION["signed_in"]) || $_SESSION["signed_in"] !== true) {
            header("Location: /signin/?continue=https://accounts.assembl.ch/user/");
            die();
        }
        else {
            header("Location: /user/?id=".$_SESSION["userdata"]["uid"]);
            die();
        }
    }
    else {
        require("import/assembldb.php");
        $connection = AssemblDB::getAccountsConnection();

        $sql = "SELECT * FROM `users`.`userdata` WHERE `uid`='".AssemblDB::makeSafe($_GET["id"], $connection)."' LIMIT 1";
        $result = mysqli_query($connection, $sql);
        if (mysqli_num_rows($result) < 1) {
            die("User with AssemblID ".$_GET["id"]." does not exist");
        }
        $userData = mysqli_fetch_assoc($result);

        $sql = "SELECT * FROM `users`.`orcid` WHERE `uid`='".AssemblDB::makeSafe($_GET["id"], $connection)."' LIMIT 1";
        $result = mysqli_query($connection, $sql);
        $orcidData = mysqli_fetch_assoc($result);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?PHP echo $userData["name"]; ?>'s account - Assembl</title>
        <base href="https://accounts.assembl.ch/" />
        <link rel="stylesheet" href="/loginstyles.css" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
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
                    <h2>User Overview</h2>
                    <hr />

                    <label>Assembl ID</label>
                    <input class="assembl-input" type="text" readonly value="<?PHP echo $userData["uid"]; ?>" />

                    <label>Name</label>
                    <input class="assembl-input" type="text" readonly value="<?PHP echo $userData["name"]; ?>" />

                    <label>Registered on</label>
                    <input class="assembl-input" type="text" readonly value="<?PHP echo $userData["registered_on"]; ?>" />

                    <label>Organization Affiliation</label>
                    <input class="assembl-input" type="text" readonly value="<?PHP echo $userData["org_affiliation"]; ?>" />

                    <label>ORCID iD</label>
                    <?PHP if (!empty($orcidData["orcid_id"])) { ?>
                        <input class="assembl-input" type="text" readonly value="<?PHP echo $orcidData["orcid_id"]; ?>" />
                        <small style="display: block; text-align: left;"><a href="https://orcid.org/<?PHP echo $orcidData["orcid_id"]; ?>" target="_blank">View ORCID record</a></small>
                    <?PHP } else { ?>
                        <input class="assembl-input" type="text" readonly value="not connected with ORCID" />
                    <?PHP } ?>

                    <?PHP if (isset($_GET["closebtn"])) { ?>
                        <input style="margin-top: 32px;" type="button" class="assembl-btn full-width" id="close-win" value="Close" onclick="window.close();" />
                    <?PHP } ?>
                </div>
            </div>
        </div>
    </body>
</html>
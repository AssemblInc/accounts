<?PHP
    error_reporting(E_ALL); ini_set('display_errors', 1);

    require_once('../import/assembldb.php');

    require("../import/sessionstart.php");

    require("../api/requirelogin.php");

    $_SESSION["settings_errors"] = array();

    $connection = AssemblDB::getAccountsConnection();

    if (isset($_POST["settings-form-name"])) {
        if (!empty($_POST["settings-form-name"])) {
            if ($_POST["settings-form-name"] !== $_SESSION["userdata"]["name"]) {
                $sql = "UPDATE `users`.`userdata` SET `name`='".AssemblDB::makeSafe($_POST["settings-form-name"], $connection)."' WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                $result = mysqli_query($connection, $sql);
            }
        }
        else {
            $_SESSION["settings_errors"]["name"] = "Name cannot be empty";
        }
    }

    if (isset($_POST["settings-form-birth-date"])) {
        if (!empty($_POST["settings-form-birth-date"])) {
            if ($_POST["settings-form-birth-date"] !== $_SESSION["userdata"]["birth_date"]) {
                $birthDate = strtotime($_POST["settings-form-birth-date"]);
                if ($birthDate) {
                    $longAgoDate = strtotime("-150 year -1 day");
                    $today = strtotime("+1 day");
                    if ($birthDate < $longAgoDate) {
                        $_SESSION["settings_errors"]["birth-date"] = "Are you sure you are over 150 years old...?";
                    }
                    else if ($birthDate > $today) {
                        $_SESSION["settings_errors"]["birth-date"] = "You are born in the future? How are the flying cars?";
                    }
                    else {
                        $birthDate = date('Y-m-d', $birthDate);
                        $_SESSION["settings_errors"]["birth-date"] = $birthDate;
                        $sql = "UPDATE `users`.`userdata` SET `birth_date`=STR_TO_DATE('".AssemblDB::makeSafe($birthDate, $connection)."', '%Y-%m-%d') WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                        $result = mysqli_query($connection, $sql);
                    }
                }
                else {
                    $_SESSION["settings_errors"]["birth-date"] = "Invalid birth date";
                }
            }
        }
        else {
            $_SESSION["settings_errors"]["birth-date"] = "Birth date cannot be empty";
        }
    }

    if (isset($_POST["settings-form-email"])) {
        if (!empty($_POST["settings-form-email"])) {
            if ($_POST["settings-form-email"] !== $_SESSION["userdata"]["email_address"]) {
                if (filter_var($_POST["settings-form-email"], FILTER_VALIDATE_EMAIL)) {
                    if (AssemblDB::getUIDByEmail($_POST["settings-form-email"]) === false) {
                        $sql = "UPDATE `users`.`userdata` SET `email_address`='".AssemblDB::makeSafe($_POST["settings-form-email"], $connection)."' WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                        $result = mysqli_query($connection, $sql);
                    }
                    else {
                        $_SESSION["settings_errors"]["email"] = "An account with this e-mail address already exists";
                    }
                }
                else {
                    $_SESSION["settings_errors"]["email"] = "Invalid e-mail address";
                }
            }
        }
        else {
            $_SESSION["settings_errors"]["email"] = "E-mail address cannot be empty";
        }
    }

    if (isset($_POST["settings-form-org-affiliation"])) {
        if (!empty($_POST["settings-form-org-affiliation"])) {
            if ($_POST["settings-form-org-affiliation"] !== $_SESSION["userdata"]["org_affiliation"]) {
                $sql = "UPDATE `users`.`userdata` SET `org_affiliation`='".AssemblDB::makeSafe($_POST["settings-form-org-affiliation"], $connection)."' WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
                $result = mysqli_query($connection, $sql);
            }
        }
        else {
            $sql = "UPDATE `users`.`userdata` SET `org_affiliation`=NULL WHERE `uid`='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
            $result = mysqli_query($connection, $sql);
        }
    }

    $sql = "SELECT * FROM `users`.`userdata` WHERE uid='".AssemblDB::makeSafe($_SESSION["userdata"]["uid"], $connection)."' LIMIT 1";
    $result = mysqli_query($connection, $sql);
    $_SESSION["userdata"] = mysqli_fetch_assoc($result);

    header("Location: /settings/");
    die();
?>
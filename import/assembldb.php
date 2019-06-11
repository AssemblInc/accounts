<?PHP
    class AssemblDB {
        static function getLoginsConnection() {
            $connection = mysqli_connect("localhost","assembllogins","***REMOVED_ASSEMBL_DB_LOGINS_PW***");
            mysqli_set_charset($connection, 'utf8mb4');
            return $connection;
        }

        static function getAccountsConnection() {
            $connection = mysqli_connect("localhost","assemblaccounts","***REMOVED_ASSEMBL_DB_ACCOUNTS_PW***");
            mysqli_set_charset($connection, 'utf8mb4');
            return $connection;
        }

        static function getKeysConnection() {
            $connection = mysqli_connect("localhost","assemblkeys","***REMOVED_ASSEMBL_DB_KEYS_PW***");
            mysqli_set_charset($connection, 'utf8mb4');
            return $connection;
        }

        static function getOrganizationsConnection() {
            $connection = mysqli_connect("localhost","assemblorgs","***REMOVED_ASSEMBL_DB_ORGS_PW***");
            mysqli_set_charset($connection, 'utf8mb4');
            return $connection;
        }

        static function getSetupConnection() {
            $connection = mysqli_connect("localhost","assemblsetup","***REMOVED_ASSEMBL_DB_SETUP_PW***");
            mysqli_set_charset($connection, 'utf8mb4');
            return $connection;
        }

        static function makeSafe($string, $connection) {
            if (empty($connection)) {
                throw new Exception('No mysqli_connection has been given as an argument');
                return null;
            }
            $string = stripslashes($string);
            $string = htmlentities($string);
            $string = strip_tags($string);
            $string = mysqli_real_escape_string($connection, strip_tags($string));
            return $string;
        }

        static function createID($length) {
            $code = "";
            $possible = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; 
            for ($i = 0; $i < $length; $i++) { 
                $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            }

            return $code;
        }

        static function createUID() {
            $uid = "";
            $possible = "1234567890ABCDEFGHJKLMNPQRSTUVWXYZ"; 
            for ($i = 0; $i < 10; $i++) { 
                $uid .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            }
            $uid = "AS".strtoupper($uid);

            if (self::accountExistsByID($uid)) {
                return createUID();
            }

            return $uid;
        }

        static function accountExistsByID($uid) {
            $connection = self::getAccountsConnection();
            $sql = "SELECT uid FROM `users`.`accounts` WHERE uid='" . self::makeSafe($uid, $connection) . "' LIMIT 1";
            $result = mysqli_query($connection, $sql);
            if ($result == false) {
                return true;
            }
            return mysqli_num_rows($result) > 0;
        }

        static function getUIDByEmail($email) {
            $connection = self::getAccountsConnection();
            $sql = "SELECT uid FROM `users`.`userdata` WHERE email_address='" . self::makeSafe($email, $connection) . "' LIMIT 1";
            $result = mysqli_query($connection, $sql);
            if ($result == false) {
                return false;
            }
            if (mysqli_num_rows($result) == 0) {
                return false;
            }
            return mysqli_fetch_assoc($result)["uid"];
        }

        static function hashPassword($password) {
            return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
        }

        static function getUserAgentKey($user_agent) {
            $connection = self::getLoginsConnection();
            $uaHash = self::makeSafe(hash("md5", $user_agent, false), $connection);
            $sql = "SELECT ua_key FROM `user_agents`.`ua` WHERE hash='".$uaHash."' LIMIT 1";
            $result = mysqli_query($connection, $sql);
            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO `user_agents`.`ua` (val, hash) VALUES ('".self::makeSafe($user_agent, $connection)."', '".$uaHash."')";
                $result = mysqli_query($connection, $sql);
                return mysqli_insert_id($connection);
            }
            else {
                return intval(mysqli_fetch_assoc($result)["ua_key"]);
            }
        }
    }
?>
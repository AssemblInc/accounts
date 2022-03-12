<?PHP
    require_once(dirname(__FILE__) . "/../import/sessionstart.php");

    function encodeURIComponentRL($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

    $base_url_rl = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
    $url_rl = $base_url_rl . $_SERVER["REQUEST_URI"];
    $encoded_url_rl = encodeURIComponentRL($url_rl);
    if (!isset($_SESSION["signed_in"]) || $_SESSION["signed_in"] !== true) {
        header("Location: https://accounts.assembl.net/signin/?continue=".$encoded_url_rl);
        die();
    }
    else {
        // do nothing. page that included this script will continue normally, since the user is signed in.
    }
?>

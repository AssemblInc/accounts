<?PHP
    function encodeURIComponentC($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

    $continueUrl = "https://accounts.assembl.net/signin/?step=signed_in";
    $encodedContinueUrl = encodeURIComponentC($continueUrl);
    $urlSpecified = false;
    $signInReason = null;
    $productName = "Assembl";
    if (isset($_GET["continue"]) && !empty($_GET["continue"])) {
        $parsedUrl = parse_url($_GET["continue"]);
        if ($parsedUrl !== false) {
            $continueUrl = $_GET["continue"];
            $encodedContinueUrl = encodeURIComponentC($continueUrl);
            $urlSpecified = true;
            if ($parsedUrl["host"] != "accounts.assembl.net") {
                $productName = $parsedUrl["host"];
                switch($parsedUrl["host"]) {
                    case "assembl.net":
                    case "www.assembl.net":
                    case "m.assembl.net":
                        $productName = "Assembl";
                        break;
                    case "chronos.assembl.net":
                        $productName = "Assembl Chronos";
                        break;
                }
                $signInReason = "Sign in to continue to ".$productName;
            }
        }
    }
?>

<?PHP
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Sign in to Assembl</title>
        <link rel="stylesheet" href="https://assembl.science/import/css/simple.css" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/ico" href="favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#193864" />
    </head>
    <body>
        <h1><a href="https://assembl.science/">Assembl</a></h1>
        <?PHP
            if (isset($_GET["code"]) && !empty($_GET["code"])) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://orcid.org/oauth/token");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                    'client_id' => '***REMOVED_ORCID_CLIENT_ID***',
                    'client_secret' => '***REMOVED_ORCID_CLIENT_SECRET***',
                    'grant_type' => 'authorization_code',
                    'code' => $_GET["code"],
                    'redirect_uri' => 'https://accounts.assembl.science/callback/orcid/'
                )));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                if ($response !== false) {
                    $jsonResponse = json_decode($response, true);
                    $jsonResponse["assembl_id"] = "AS" . strtoupper(substr(hash("sha256", $jsonResponse["orcid"]), 0, 10));
                    if (isset($_SESSION["signin_return_key_as_json"]) && $_SESSION["signin_return_key_as_json"] == true) {
                        ob_end_clean();
                        echo json_encode($jsonResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    }
                    else {
                        $_SESSION["orcid_data"] = $jsonResponse;
                        ?>
                        <h2>You are now signed in to Assembl.</h2>
                        <p>This part hasn't been finished yet. <a href="https://assembl.science">Return to the home page</a></p>
                        <?PHP
                    }
                }
                else {
                    ?>
                    <h2>Something went wrong</h2>
                    <p>Could not sign in with ORCID. Please try again later.</p>
                    <br /><br />
                    <p><i><small>Could not trade authorization code for access token</small></i></p>
                    <?PHP
                }
            }
            else if (isset($_GET["error"])) {
                ?>
                <h2>Something went wrong</h2>
                <p>Could not sign in with ORCID. Please try again later.</p>
                <br /><br />
                <p><i><small><?PHP echo $_GET["error_description"]; ?></small></i></p>
                <?PHP
            }
            else {
                ?>
                <h2>Something went wrong</h2>
                <p>Could not sign in with ORCID. Please try again later.</p>
                <br /><br />
                <p><i><small>GET parameter 'code' but also 'error' is not set <?PHP print_r($_POST); ?></small></i></p>
                <?PHP
            }
        ?>
    </body>
</html>
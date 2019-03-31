<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Sign in to Assembl</title>
        <link rel="stylesheet" href="https://assembl.science/import/css/simple.css" />
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
                    'redirect_uri' => 'https://accounts.assembl.science/callback/orcid.php'
                )));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                if ($response !== false) {
                    if (isset($_SESSION["signin_return_key_as_json"]) && $_SESSION["signin_return_key_as_json"] == true) {
                        ?>
                        <h2>You are now signed in to Assembl.</h2>
                        <p>This part hasn't been finished yet. We're working on it as we speak!</p>
                        <br /><br />
                        <p><i><small><?PHP print_r($_POST); ?></small></i></p>
                        <?PHP
                    }
                    else {
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
            else {
                ?>
                <h2>Something went wrong</h2>
                <p>Could not sign in with ORCID. Please try again later.</p>
                <br /><br />
                <p><i><small>GET parameter 'code' is not set</small></i></p>
                <?PHP
            }
        ?>
    </body>
</html>
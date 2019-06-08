<?PHP
    header("Location: https://accounts.assembl.ch/signin/?".http_build_query($_GET));
    exit();
?>
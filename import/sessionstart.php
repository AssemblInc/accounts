<?PHP
    /*
        if (empty(session_name())) {
            session_name("assembl.net");
        }
    */
    // ini_set('session.cookie_domain', '.assembl.net');
    session_set_cookie_params(0, '/', '.assembl.net');
    session_start();
?>

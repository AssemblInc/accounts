<?PHP
    /*
        if (empty(session_name())) {
            session_name("assembl.ch");
        }
    */
    // ini_set('session.cookie_domain', '.assembl.ch');
    session_set_cookie_params(0, '/', '.assembl.ch');
    session_start();
?>
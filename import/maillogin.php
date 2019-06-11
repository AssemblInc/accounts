<?PHP
    function mailLogin($mail) {
        $mail->isSMTP();
        $mail->Host = "***REMOVED_ASSEMBL_MAIL_NOREPLY_HOST***";
        $mail->SMTPAuth = true;
        $mail->Username = "no-reply@assembl.us";
        $mail->Password = "***REMOVED_ASSEMBL_MAIL_NOREPLY_PW***";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('no-reply@assembl.us', 'Assembl');
        return $mail;
    }
?>
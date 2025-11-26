<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('pathTo')) {
    function pathTo($destination) {
        echo "<script>window.location.href = '/GlitterTails/$destination.php'</script>";
    }
}

if (!isset($_SESSION['status']) || $_SESSION['status'] == 'invalid') {
    /* Set status to invalid */
    $_SESSION['status'] = 'invalid';

    /* Unset user data */
    unset($_SESSION['Username']);

    /* Redirect to login page */
    pathTo('index');
}
?>


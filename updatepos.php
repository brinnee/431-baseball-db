<?php
if (isset($_POST['cancel'])) {
    header("Location: viewteam.php");
    exit;
}

// Continue processing form if it's not a cancel action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['cancel'])) {
    header("Location: editanimals.php");
    exit;

}
?>
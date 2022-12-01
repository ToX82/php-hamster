<?php

if (isset($_SESSION['msg'])) {
    $text = $_SESSION['msg']['text'];
    $style = $_SESSION['msg']['type'];

    switch ($style) {
        case "success":
            $style = 'green';
            break;

        case "notice":
            $style = 'blue';
            break;

        case "warning":
            $style = 'yellow';
            break;

        case "error":
            $style = 'red';
            break;
    }

    echo "<div class='sessionMsg hide' data-color='" . $style . "'>" . $text . "</div>";
    unset($_SESSION['msg']);
}

<?php
    require_once __DIR__ . "/../../../../wp-load.php";
    require_once __DIR__ . "/../helpful.php";

    switch($_POST["action"]){
        case "feedback":
            HelpfulQmark::saveFeedback();
            break;
    }
?>
<?php

$xajax->register(XAJAX_FUNCTION,'viewportScale');

function viewportScale($scale){
    $_SESSION['viewportScale'] = $scale;
}
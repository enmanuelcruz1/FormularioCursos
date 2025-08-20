<?php

require_once("registrar.php");

function publicarNotificacion($mensaje)
{
    echo "<div class='error-container'>
    <div class='error'>
        <h2 class='message'><?= $mensaje ?></h2>
    </div>
</div>";
}
?>
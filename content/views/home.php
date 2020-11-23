<?php
    $render = new Render();
    $render->prop("prop.php", ["hello"=>"hi"]); 
    $DOM    = new DOM(); 
?>
<form action="<?=_URL_?>/core/php/amvc.api.php" callback="alert" async="true" method="POST">
    <?=$DOM->interaction_api("api1.php")?>
    <input type="submit" value="save">
</form>
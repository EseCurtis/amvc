<?php
   $app_data = $PROJECT_INFO;
?>
<div class="app__main">
   <div class="app__logo">
      <img src="<?=_URL_.$app_data["logo_src"]?>" alt="amvc">
   </div>
   <div class="app__name">
      <?=$app_data["app_name"]?>
   </div>
   <div class="app__description">
      <?=$app_data["description"]?>
      <a href="https://esecodes.com/amvc/">see documentation here</a>
   </div>
   <div class="app__version">
      <p>version - <?=$app_data["version"]?></p> 
   </div>
   <div class="app__author">
      <?=$app_data["author"]?>
   </div>
   <div>
      configure your app in "./projects.json"
   </div>
   <div>
   start by setting your ebsite url to your project base url : Example - <b>"website_url" : "localhost/myproject"</b>
   </div>
</div>
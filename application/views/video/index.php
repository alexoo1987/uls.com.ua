<div class="container">
    <br />
    <h1>Наши видео</h1>
    <?php foreach ($videos as $video){
        echo $video->url;
        echo "<br>";
    }?>
    <div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,nocounter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir"></div>
</div>

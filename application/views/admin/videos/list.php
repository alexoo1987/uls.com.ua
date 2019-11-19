<div class="container">
	<br />
	<h1>Наши видео</h1>
	<?php foreach ($videos as $video){
		echo $video->url;
		echo "<br />";
	} ?>
</div>
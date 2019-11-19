<form action="<?= URL::site('find/index'); ?>" method="GET" style="padding-right: 15px; padding-left: 15px"
	  class="navbar-form navbar-main-search navbar-main-search-category no-border" role="search">
	<div class="form-group">
		<input class="form-control border_input" type="text" name="art" placeholder="Поиск запчасти..."
			   value="<?= (!empty($_GET['art'])) ? $_GET['art'] : '' ?>" required/>
	</div>
	<a class="fa fa-search navbar-main-search-submit" href="#" onclick="$(this).closest('form').submit()"></a>
<!--	<a class="vin_code_long" href="http://catalog.eparts.kiev.ua/">Поиск по VIN code</a>-->
</form>
<script type="application/ld+json">
{
  "@context" : "http://schema.org",
  "@type" : "WebSite", 
  "name" : "Интернет-магазин автозапчастей Куряков Eparts",
  "url" : "http://ulc.com.ua/",
  "potentialAction" : {
    "@type" : "SearchAction",
    "target" : "http://ulc.com.ua/find/index?art={search_term}",
    "query-input" : "required name=search_term"
  }                     
}\
</script>
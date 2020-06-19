</body>
</html>


<script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/1.9.0/i18next.min.js" type="text/javascript"></script>
<script>
//window.onload=function(){
$(document).ready(function(){
	i18n.init({ 
		lng: 'en',
		load: 'languageOnly',
		resGetPath: '/locales/__lng__.json', 
		//fallbackLng: false, 
		debug : false
	}, function (t){ 
		$("html").i18n(); 
	});
});
</script>

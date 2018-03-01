{

	$(".button-collapse").sideNav({closeOnClick: true});
    $('.collapsible').collapsible();
    $('select').material_select();


    // Serviceworker register
    if ('serviceWorker' in navigator) {
    	navigator.serviceWorker.register(location.origin+'/sw.js').then(function(){console.log('[SWORKER registered]')});
	}
}
/**
 * parseQueryString function. Cette fonction pourrait aller dans une librairie, elle décode les paramètres de l'include et les renvoie en Object.
 * @param scriptName : Nom de l'include pour lequel on cherche la querystring.
 * @access public
 * @return void
 */
function parseQueryString(scriptName){
	 var _get = {};
	 var scripts = document.getElementsByTagName('script');
	 for (var i=0;scripts.length>i;i++)
	 {
		if (scripts[i].src.search(scriptName) != -1){
			var vars = scripts[i].src.substring(scripts[i].src.indexOf('?')+1, scripts[i].src.length).split("&");
			for (x in vars)
			{
				var k = vars[x].split("=");
				_get[unescape(k[0])] = unescape(k[1]);
			}
		}
	 }
	 return (_get);
}


jQuery(document).ready(function($) {

	var hashdata = new Object();
	
	jQuery.each(window.location.hash.replace(/^#/,'').split('/'), function(i,t){
	   hashdata[i] = t;
	});

	// On récupère les paramètres de l'include (le nom du lien et le nom de classe des boutons).
	var param = parseQueryString("galerie.ajax.js");
	var currentGallery = 0;
	var imagePerGallery = 9;

	// La fonction de loading est indépendante des boutons puisqu'elle va chercher tout ce qui est selectionné, 
	// on a pas besoin de la répéter pour chaque bouton.
	$("#resultat").bind("loadSelection", function(e, navig){
		if(!navig) navig = 0;
		if(navig != 0){
			currentGallery += navig;
			// History manager
			//window.location.hash = "#gallery/" + currentGallery + '/';
			req = { currentGallery : currentGallery, imagePerGallery : imagePerGallery, lang : param.lang };
			$(this).html('<div style="min-height:600px;"><img src="/benito/images/common/loading.gif" width="32" height="32" class="loading" /></div>');
			$.ajax({
				type: "POST",
				url: param.link,
				data: req ,
				success: function(x){
					$('#resultat').html(x);
					$('#resultat').trigger("checkNavigation");
				}
			});
		}
	}).bind("checkNavigation", function(){
		if(currentGallery*imagePerGallery + imagePerGallery > $('#resultat div').attr("total")) $(".btnext").attr("disabled", "disabled");
		else $(".btnext").removeAttr("disabled");
		
		if(currentGallery == 0) $(".btprev").attr("disabled", "disabled");
		else $(".btprev").removeAttr("disabled");
	})

	$("#resultat").trigger("loadSelection", parseInt(hashdata[1])).trigger("checkNavigation");
	
	$(".btprev").click(function(){
		if(!$(this).attr("disabled")) $("#resultat").trigger("loadSelection", -1);
		return false;
	});
	
	$(".btnext").click(function(){
		if(!$(this).attr("disabled")) $("#resultat").trigger("loadSelection", 1);
		return false;
	});


});
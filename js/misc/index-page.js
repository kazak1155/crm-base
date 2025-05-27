$(function(){
	$('.menu-main-ul').ddmenu({
		click:function(e,options){
			var link = $(this).children('a').attr('href');
			//console.log(link);
			//alert(link);
			var search = new RegExp('.*'+link+'$');
			var cur_link = search.test(window.location.search);
			//if(link.match( /templates/i )){
			//	location.href = link;
			//}else
			if(link){
				if(window.location.search.length === 0)
					location.href = '?reference='+link;
				else if(cur_link == false)
					window.open('?reference='+link);
				return;
			}
			if($(this).hasClass('direct-actions'))
			{
				var type = $(this).children('a')[0].id;
				switch(type)
				{
					case 'filestream':
						window.open('misc/file_tree/?fs='+ encodeURIComponent('{"table":"global","id":"global"}'));
						break;
					case 'closeFrame':
						$('.main-frame').remove();
						window.history.replaceState("", "", "?");
						document.title = 'CRM';
						break;
					case 'killss':
						$.ajaxShort({data:{action:'kill_sess'},reload:true});
						break;
					case 'logout':
						if (/Trident/.test(navigator.userAgent)) //?IE
							document.execCommand("ClearAuthenticationCache");
						else {
							window.location = window.location.href.replace(
								window.location.protocol + '//',
								window.location.protocol + '//' + 'logout:password@'
							);
						}
						setTimeout(function()
						{
							$.ajaxShort({data:{action:'kill_sess'},reload:true});
						}, 10);
						break;
					case 'eraseCache':
						//localStorage.clear();
						localStorage.removeItem("contragent02");
						$.ajaxShort({data:{action:'flush_cache'},reload:true});
						break;
					/*
					case 'langRu':
						setUserPref({oper:'set_user_pref',prefName:'lang',prefVal:'ru',removePrefName:'trns'},true);
						break;
					case 'langEn':
						setUserPref({oper:'set_user_pref',prefName:'lang',prefVal:'en',removePrefName:'trns'},true);
						break;
					case 'langIt':
						setUserPref({oper:'set_user_pref',prefName:'lang',prefVal:'it',removePrefName:'trns'},true);
						break;
					*/
					default:break
				}
			}
		}
	});
});

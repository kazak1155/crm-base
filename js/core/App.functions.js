function isDevice()
{
	if (/Mobi/.test(navigator.userAgent))
    	return true;
    else
    	return false;
}
//ser get
function val_get(keyName) // http://stackoverflow.com/a/21210643
{
	var get_vals = {};
	var splitted_searh = location.search.substr(1).split("&");
	splitted_searh.forEach(function(item)
	{
		get_vals[item.split("=")[0]] = item.split("=")[1]
	});
	return keyName ? get_vals[keyName] : get_vals;
}
//Check if element in current viewport
function isElementInViewport (el)
{
	var bol = true;
    if (el instanceof jQuery)
    	el = el[0];

    var rect = el.getBoundingClientRect();

    bol =
    (
        rect.top >= 0 && rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
    return bol;
}
//Check if element is overflowed
function checkOverflow(el,of_el,direction)
{
	var curOverflow,isOverflowing,prev_els;
	var prev_els_wh = {
		width:0,
		height:0
	};
	if(typeof of_el === typeof undefined)
	{
		curOverflow = el.style.overflow;
		if ( !curOverflow || curOverflow === "visible" )
			el.style.overflow = "hidden";

		isOverflowing = el.clientWidth < el.scrollWidth || el.clientHeight < el.scrollHeight;

		if(typeof direction !== typeof undefined)
		{
			if(direction === 'horizontal')
				isOverflowing = el.clientWidth < el.scrollWidth;
			else if(direction === 'vertical')
				isOverflowing = el.clientHeight < el.scrollHeight;
		}
		else
			isOverflowing = el.clientWidth < el.scrollWidth || el.clientHeight < el.scrollHeight;

		el.style.overflow = curOverflow;
	}
	else
	{
		curOverflow = of_el.style.overflow;
		if ( !curOverflow || curOverflow === "visible" )
			of_el.style.overflow = "hidden";


		prev_els = $(el).prevAll('td:visible').andSelf();
		if(prev_els.length > 0)
		{
			$.each(prev_els,function(i,v)
			{
				prev_els_wh.width += v.clientWidth;
				prev_els_wh.height += v.clientHeight;
			})
		}
		if(typeof direction !== typeof undefined)
		{
			if(direction === 'horizontal')
				isOverflowing = prev_els_wh.width > of_el.clientWidth;
			else if(direction === 'vertical')
				isOverflowing = prev_els_wh.height > of_el.clientHeight;
		}
		else
			isOverflowing = prev_els_wh.width > of_el.clientWidth || prev_els_wh.height > of_el.clientHeight;

		of_el.style.overflow = curOverflow;
	}
	return isOverflowing;
}
//Find object in array of objects, fuck you underscope!
function containsObject(obj, list,fieldName,op,fieldValue)
{
    for (var i = 0; i < list.length; i++)
    {
		if (typeof list[i][fieldName] === 'object' && typeof obj[fieldName] === 'object' )
		{
			if((JSON.stringify(list[i][fieldName]) === JSON.stringify(obj[fieldName])))
				return true;
			else
				return false;
			break;
		}
		else
		{
			if (list[i][fieldName] === obj[fieldName] && list[i][op] === obj[op] && list[i][fieldValue] === obj[fieldValue])
	            return true;
			break;
	    }
    }
    return false;
}
//Redirect to php pdf
// TODO this shit need complete remake...like there was smth xDDD
function getPDF(phpsource)
{
	window.open((document.URL)+phpsource,'pdf');
}

function get_file_url(get_data,filename,url)
{

	var get_url = '?';
	if(typeof url === typeof undefined)
	{
		if(get_data.hasOwnProperty('type'))
		{
			switch(get_data.type)
			{
				case 'word':
					/* TODO word tmpl */
					url = '/core/file_system/word/word_tmpl';
					break;
				case 'excel':
					url = '/core/file_system/excel/excel_tmpl';
					break;
				case 'pdf':
					/* TODO smth pdf tmpl ? How... */
					url = '';
					break;
				default:
					return $.alert('Unkown file type');
					break;
			}
		}
		else
			return $.alert('No url and no file type set');
	}
	else if(url instanceof Object)
		url = '/core/file_system/'+ url.prefix +'/' + url.folder + '/' + url.prefix;
	if(!get_data.hasOwnProperty('qry') && !url)
		return $.alert('No qry set');
	get_url += 'qry='+encodeURIComponent(JSON.stringify(get_data.qry));
	if(!get_data.hasOwnProperty('reference'))
		return $.alert('No reference set');
	get_url += '&reference='+get_data.reference;

	if(get_data.hasOwnProperty('qw'))
		get_url += '&qw='+encodeURIComponent(JSON.stringify(get_data.qw));
	filename = typeof filename === typeof undefined ? 'Unnamed' : filename;
	get_url += '&fn='+encodeURIComponent(filename);
	if(get_data.hasOwnProperty('etc'))
		get_url += '&etc='+encodeURIComponent(JSON.stringify(get_data.etc))
	window.location = url + get_url;
}
//Redirect to php excel
function getExcel(statment,filters,filename,url)
{
	typeof statment !== typeof undefined ? statment = JSON.stringify(statment): $.alert('Smth Wrong!');
	typeof filters !== typeof undefined ? filters = JSON.stringify(filters): filters = null;
	typeof url === typeof undefined ? url = '/php/main/file_system/excel/excel_tmpl': null;

	url = url + '?fileName='+filename+'&qry='+statment+'&filters='+filters;
	window.location = url;
}
//<option> Creator
function optionCreate(value)
{
	var delimArray = value.split(';')
	var summary = new String;
	var sepArray;
	for (var i=0; i < delimArray.length; i++)
	{
		sepArray = delimArray[i].split(':');
		option = "<option value='"+sepArray[0]+"'>"+sepArray[1]+"</option>";
		summary += option;
	}
	return summary;
}
//Set Selection range
function selectInnerText(element)
{
    var doc = document;
    var text = doc.getElementById(element);
    var range,selection;
	if (doc.body.createTextRange)
	{
		range = document.body.createTextRange();
		range.moveToElementText(text);
		range.select();
	}
	else if (window.getSelection)
	{
		selection = window.getSelection();
		range = document.createRange();
		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}
//Current Date yyyy-mm-dd/dd.mm.yyyy
function getDate(region,server,init,precise)
{
	var currentTime;
	if(init)
	{
		/* retarded fix, FF/SAFARI cant parse date */
		init = init.replace(/\s+/g, 'T') + 'Z';
		currentTime = new Date(init);
	}
	else
		currentTime = new Date();
	var date;
	var month = (currentTime.getMonth() + 1).toString(),
	day = currentTime.getDate().toString(),
	year = currentTime.getFullYear().toString();
	var hours = currentTime.getHours().toString(),
	minutes = currentTime.getMinutes().toString();

	if(minutes < 10)
		minutes = '0'+ minutes;
	if (day < 10)
		day ='0'+day;
	if (month < 10)
		month = '0'+month;

	if(server == true)
		date = year + month + day;
	else
		date = region === true ? day + "." + month + "." + year : year + "-" + month + "-" + day;

	if(precise == true)
	{
		date = date + ' ' + hours + ':' + minutes;
	}
	return date;
}
function getTime(seconds)
{
	var currentTime = new Date();
	return currentTime.getHours().toString() +':'+ currentTime.getMinutes().toString();
}
//Destroy grid,clear container
function destroy(target)
{
	if(target)
	{
		$("table[id*='"+ id +"']").jqGrid('GridDestroy');
	}
	else
	{
		if($('#gridcontainer').children().length > 0){
			$('.gridclass').jqGrid('GridDestroy');
			$('#container').find('table').remove();
			$('.ui-widget').remove();
			$('.ui-datepicker-div').remove();
			$('.ui-multiselect-menu').remove();
			document.getElementById('gridcontainer').innerHTML = "";
		}
	}
}
// detect browser via userAgent, client sniffing "My Dad Says That's For Pussies"
function get_agent(isLame)
{
	var browser;
	if((navigator.userAgent.indexOf("Opera") || navigator.userAgent.indexOf('OPR')) != -1 )
		browser = 'Opera';
	else if(navigator.userAgent.indexOf("Chrome") != -1 )
		browser = 'Chrome';
	else if(navigator.userAgent.indexOf("Safari") != -1)
		browser = 'Safari';
	else if(navigator.userAgent.indexOf("Firefox") != -1 )
		browser = 'Firefox';
	else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
		browser = 'IE';
	else
		browser = 'shit';
	if(isLame == true)
	{
		if(browser != 'Opera' && browser != 'Chrome' && browser != 'Firefox')
			return true;
	}
	else
		return browser;
}
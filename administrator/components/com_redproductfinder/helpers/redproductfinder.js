function getHTTPObject()
{
	var xhr = false;
	if (window.XMLHttpRequest)
	{
		xhr = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try
		{
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				xhr = false;
			}
		}
	}
	return xhr;
}
function showBox(div) {
	var opendiv = document.getElementById(div);
	
	if (opendiv.style.display == 'block') opendiv.style.display = 'none';
	else opendiv.style.display = 'block';
	return false;
}
function add_dependency(type_id,tag_id,product_id){
	var request;
	request = getHTTPObject();
	var arry_sel = new Array();
	if(document.getElementById('sel_dep'+type_id+'_'+tag_id))
	{
		var j=0;
		var selVal = document.getElementById('sel_dep'+type_id+'_'+tag_id);
		for(var i=0;i<selVal.options.length;i++)
			if(selVal.options[i].selected)
				arry_sel[j++] = selVal.options[i].value;
	}
	var dependent_tags = "";
	dependent_tags = arry_sel.join(",");
	if(document.getElementById('product_id'))
		product_id = document.getElementById('product_id').value;
	args = "dependent_tags="+dependent_tags+"&product_id="+product_id+"&type_id="+type_id+"&tag_id="+tag_id;
	var url = "index.php?tmpl=component&option=com_redproductfinder&controller=associations&task=savedependent&"+args;
		
	request.onreadystatechange=function() {
		if(request.readyState == 4)
		{
			alert(request.responseText);
		}
	}
	request.open("GET", url, true);
	request.send(null);
}
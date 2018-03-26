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
function getDependent(ind)
{
	var request;
	request = getHTTPObject();
	var ele = document.adminForm.elements;
	var hostname = window.location.hostname;
	var pathname = window.location.pathname;
	var j=0;
	var args = new Array();
	var prev_type_id ="0" ;
	var val = "";
	for(var i=0;i<ele.length;i++)
	{
		if(ele[i].type=='select-one')
		{	
			args[j++] = ele[i].id+"::"+ele[i].value;
		}
		if(ele[i].type=='checkbox')
		{	
			if(prev_type_id=="0")
				prev_type_id = ele[i].id;
			if(prev_type_id==ele[i].id)
			{
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}
				else
				{
					val += '';
				}
			}
			else 
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}				
			}

			if(ele[i+1].type!="checkbox" || ((i+1)==ele.length))
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
			}
		}
	}
	
	var arg_var = 'selected_tag='+args.join("`");
	
	var url = "http://"+hostname+pathname+"?tmpl=component&option=com_redproductfinder&controller=redproductfinder&ajax=1&task=Redproductfinder_ajax&"+arg_var+"&ind="+ind;
	
	request.onreadystatechange=function() {
		if(request.readyState == 4)
		{
			
			//alert(request.responseText);
			document.getElementById('rep_search').innerHTML = request.responseText;
						
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "from_startdate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "from_startdate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});
			
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "to_enddate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "to_enddate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});			
			
		}
	}
	request.open("GET", url, true);
	request.send(null);	
}
function mod_getDependent(ind,modid)
{
	
	var frmname="mod_rep_search"+modid
	var hostname = window.location.hostname;
	var pathname = window.location.pathname;
	var request;
	request = getHTTPObject();
	var ele = eval("document.redPRODUCTFINDERFORM"+modid).elements;
	var j=0;
	var args = new Array();
	var prev_type_id ="0" ;
	var val = "";
	for(var i=0;i<ele.length;i++)
	{
		if(ele[i].type=='select-one')
		{	
			args[j++] = ele[i].id+"::"+ele[i].value;
		}
		if(ele[i].type=='checkbox')
		{	
			if(prev_type_id=="0")
				prev_type_id = ele[i].id;
			if(prev_type_id==ele[i].id)
			{
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}
				else
				{
					val += '';
				}
			}
			else 
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}				
			}

			if(ele[i+1].type!="checkbox" || ((i+1)==ele.length))
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
			}
		}
	}
	var arg_var = 'selected_tag='+args.join("`");
	
	var url = "http://"+hostname+pathname+"?tmpl=component&option=com_redproductfinder&controller=redproductfinder&ajax=1&task=Redproductfinder_ajax&"+arg_var+"&ind="+ind+"&modid="+modid+"&mod=1";
		
	request.onreadystatechange=function() {
		if(request.readyState == 4)
		{
			document.getElementById(frmname).innerHTML = request.responseText;
			
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "from_startdate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "from_startdate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});
			
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "to_enddate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "to_enddate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});
		}
	}
	request.open("GET", url, true);
	request.send(null);	
}
function plg_getDependent(ind,formid)
{
	var request;
	request = getHTTPObject();
	var ele = document.adminForm.elements;
	var hostname = window.location.hostname;
	var pathname = window.location.pathname;
	var j=0;
	var args = new Array();
	var prev_type_id ="0" ;
	var val = "";
	for(var i=0;i<ele.length;i++)
	{
		if(ele[i].type=='select-one')
		{	
			args[j++] = ele[i].id+"::"+ele[i].value;
			
		}
		if(ele[i].type=='checkbox')
		{	
			if(prev_type_id=="0")
				prev_type_id = ele[i].id;
			if(prev_type_id==ele[i].id)
			{
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}
				else
				{
					val += '';
				}
			}
			else 
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
				if(ele[i].checked)
				{
					if(val=="")
						val = ele[i].value;
					else
						val += ","+	ele[i].value;
				}				
			}

			if(ele[i+1].type!="checkbox" || ((i+1)==ele.length))
			{
				args[j++] = prev_type_id+"::"+val;
				val = "";
				prev_type_id = ele[i].id;
			}
		}
	}
	
	var arg_var = 'selected_tag='+args.join("`");
	
	var url = "http://"+hostname+pathname+"?tmpl=component&option=com_redproductfinder&controller=redproductfinder&ajax=1&task=Redproductfinder_ajax&"+arg_var+"&ind="+ind+"&formname="+formid;
	
	request.onreadystatechange=function() {
		if(request.readyState == 4)
		{
			document.getElementById('rep_search').innerHTML = request.responseText;
			
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "from_startdate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "from_startdate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});
			
			window.addEvent('domready', function() {Calendar.setup({
		        inputField     :    "to_enddate_ajax",     // id of the input field
		        ifFormat       :    "%d-%m-%Y",      // format of the input field
		        button         :    "to_enddate_ajax_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });});
		}
	}
	request.open("GET", url, true);
	request.send(null);	
}
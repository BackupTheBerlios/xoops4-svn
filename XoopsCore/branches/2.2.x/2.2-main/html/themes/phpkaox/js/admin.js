function xoops_toggleMenu(id)
{
	var element=document.getElementById("adminmenu_"+id);
	if(element.style.display=="none")
	{
		//Practical patch for I.E.
		try
		{
			element.style.display="table-row";
		}
		catch(error)
		{
			element.style.display="block";
		}
	}
	else
	{
		element.style.display="none";
	}

	xoops_buildCookie();
	return(false);
}

function xoops_buildCookie()
{
	var i;
	var cookie="";
	for(i=0; i < adminmenusize; i++)
	{
		var element=document.getElementById("adminmenu_"+i);
		if(element.style.display=="none")
			cookie+="0";
		else
			cookie+="1";

		if(i<(adminmenusize-1))
			cookie+=",";

	}
	xoops_setCookie("adminmenustate",cookie,1);

}

function xoops_applyCookie()
{
	var cookie=xoops_getCookie("adminmenustate");
	if(typeof(cookie)=="string")
	{
		var temp=cookie.split(",");

		for(i=0; i < adminmenusize; i++)
		{
			if(temp[i]==1)
			{
				xoops_toggleMenu(i);
			}
		}
	}
}

function xoops_getCookie(cookiename){
	var begin=-1;
	var stringend=-1;

	if (document.cookie.length > 0) {
		var cookies=document.cookie.split("; ");

		for(i=0;i<cookies.length;i++)
		{
			if(cookies[i].indexOf(cookiename)==0)
			{
				var temp=cookies[i].split("=");
				return(unescape(temp[1]));
			}
		}
		return false;
	}

	return false;
}

function xoops_setCookie(cookiename, value, expiredays)
{
	var ExpireDate = new Date ();
	ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 6000 * 100 * 100));
	document.cookie = cookiename + "=" + escape(value) +
	((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString())+"; path=/";

}

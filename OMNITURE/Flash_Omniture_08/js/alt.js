// JavaScript Document

window.onload=function()
{
	var b, r, t = document.getElementsByTagName("table");
	for(var i=0; i<t.length; i++)
	{	var current_t = t[i];
		if(current_t.className && current_t.className == "t700")
		
		{	b = current_t.getElementsByTagName("tbody");
			for(var j=0; j<b.length; j++)
			{	var current_b = b[j];
				r = current_b.getElementsByTagName("tr");
				for(var k=0; k<r.length; k+=2)
				{	r[k].className = "udda";
				
				}
			}
		}
	}
}
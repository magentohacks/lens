var barclaycardFlexCheckout =  new function () {
	
	this.includeCss = function(url){
		var cssId = 'barclaycard-overlay';
		if (!document.getElementById(cssId))
		{
		    var head  = document.getElementsByTagName('head')[0];
		    var link  = document.createElement('link');
		    link.id   = cssId;
		    link.rel  = 'stylesheet';
		    link.type = 'text/css';
		    link.href = url;
		    link.media = 'all';
		    head.appendChild(link);
		}
	}
	
	this.createIframe = function(url, jQ){
		var over = 
			'<div id="barclaycard-flex-overlay" class="barclaycard-flex-overlay">'+
				
					'<iframe src="'+url+'" class="barclaycard-flex-content"></iframe>'+
			'</div>';
		jQ(over).appendTo('body');
		jQ('body').addClass('barclaycard-flex-noscroll');
	}

}
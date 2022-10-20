// JavaScript Document

/*!
 * liScroll 1.0
 * Examples and documentation at: 
 * http://www.gcmingati.net/wordpress/wp-content/lab/jquery/newsticker/jq-liscroll/scrollanimate.html
 * 2007-2010 Gian Carlo Mingati
 * Version: 1.0.2.1 (22-APRIL-2011)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Requires:
 * jQuery v1.2.x or later
 * 
 */

jQuery.fn.liScroll = function(settings) {
		settings = jQuery.extend({
		travelocity: 0.07, begin : 1, mode : 1
		}, settings);		
		return this.each(function(){
							
						
				console.log('123');		
						
				var mode =  1;	 	
				var begin = 1;
				var travelocity = settings.travelocity;
				var $ulContent = jQuery(this); // this is the ul items container
				var contentWidth = 1; // this is the width our content
				var ulItems = [];
				var sw= 0;
				var sw2 = 0;
				
				$ulContent.find("li").each(function(i){
					sw++;
					ulItems.push( jQuery(this, i).html() );
					contentWidth += jQuery(this, i).outerWidth(true); 
				});
				
				var containerWidth = $ulContent.parent().parent().width();
				var $mask = $ulContent.parent();
				var $tickercontainer = $mask.parent();
				
				var mask2_left = $tickercontainer.find(".maks2-left");
				mask2_left.height($tickercontainer.height());
				
				var mask2_right = $tickercontainer.find(".maks2-right");
				mask2_right.height($tickercontainer.height());
				
				if($tickercontainer.find(".maks2-title")) {
					mask2_left.css('left',$tickercontainer.find(".maks2-title").outerWidth(true));
				}
				if($tickercontainer.find(".mask2-more")) {
					//console.log('tamano: ' + $tickercontainer.find(".mask2-more").outerWidth(true) );
					mask2_right.css('right',$tickercontainer.find(".mask2-more").outerWidth(true));
				}
				
				
				var repeats = 10;
				var content_totalWidth = contentWidth * 10;
				for(var j = 0; j<repeats ; j++) {
					for ( var i = 0; i < ulItems.length; i = i + 1 ) { 
						jQuery(this).append('<li>' + ulItems[ i ] + '</li>');
					}
				}
				
				$ulContent.find("li").each(function(i){
					jQuery(this, i).css('visibility','visible');
				});
				
				$ulContent.width(content_totalWidth + 10000);		
				$ulContent.css("right", containerWidth);		

				var totalTravel = content_totalWidth;
				var defTiming = totalTravel / travelocity;	// thanks to Scott Waye		
				
				console.log('content_totalWidth: ' + (content_totalWidth + 10000 - containerWidth));
	
				function scrollnews(spazio, tempo, exact){
					
					var gotoleft = 0;//contentWidth;
					
					//console.log('content_totalWidth: ' + content_totalWidth);
					
					
					if(mode==1 && exact ==0) {
						if(sw2>0) spazio =  spazio / repeats;
						else spazio = containerWidth + contentWidth;
					}
					sw2++;
				
					$ulContent.animate({right: '-='+ spazio}, (spazio / travelocity), "linear", function(){
							$ulContent.css("right", gotoleft); scrollnews(totalTravel, defTiming, 0);
					});
					
				
				}
				
				scrollnews(totalTravel, defTiming, 0);				
				
				$ulContent.hover(function(){
					jQuery(this).stop();
					},
					function(){
					
						var position = jQuery(this).position();
						var residualSpace = position.left + contentWidth;
						var residualTime = residualSpace/settings.travelocity;
						scrollnews(residualSpace, residualTime, 1);
						
				});			
		});	
};



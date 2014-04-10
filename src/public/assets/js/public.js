(function ( $ ) {
	"use strict";

	$(function () {
		/**************************************************************************************************************/
		$( ".sortable" ).sortable();
		
		/**************************************************************************************************************/
		
	    $( ".sortable" ).disableSelection();
	    
	    /**************************************************************************************************************/
	    /**
		* Sort a list of pages 
		*/
		$( ".sortable_list").click(function(){
	    	//alert($(this).attr('post_id'));
			var post_id = $(this).attr('post_id');
			var direction = $(this).attr('direction');
			
			//get the ul element that we wqant to sort and add its li children elements to an array
			var sbp_array = new Array();
			var ul = $("#subpages_of_" + post_id);
			ul.children("li").each(function(index, Element){
				 console.log( index + ": " + $( this ).text() );
				 var li = $(this).detach();
				 sbp_array.push(li);
				
			});
			
	    	//sort the array by the value attribute of the li htmlt element
			sbp_array.sort(function(li1, li2){
				var title1 = li1.attr('li_value');
				var title2 = li2.attr('li_value');
				if(direction == 'asc'){
					return title1.localeCompare(title2);
				}else{
					return title2.localeCompare(title1);
				};
			});
			
			for(var index = 0; index < sbp_array.length; index++){
				console.log( index + ": " + sbp_array[index].text() );
				sbp_array[index].appendTo(ul);
			}
			
	    });
		/**************************************************************************************************************/
		/**
		* Toggle a list of subpages
		*/
		$(".toggle").click(function(){
			var post_id = $(this).attr('post_id');
			//select the ul html element containing the subpages under the current post
			var ul  = $("#subpages_of_" + post_id);
			//slideToggle the list
			ul.slideToggle('slow', function(){});
		});

	});

}(jQuery));
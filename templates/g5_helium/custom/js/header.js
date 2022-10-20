/*let lastScroll = 0;
const defaultOffset = 100;
const header = document.querySelector('#g-navigation'); 
const scrollPosition = () => window.pageYOffset || document.documentElement.scrollTop;
const containHide = () => header.classList.contains('out123');


window.addEventListener('scroll', () => {
    if(scrollPosition() > lastScroll && !containHide() && scrollPosition() > defaultOffset) {
        //scroll down
        header.classList.add('out123');
    }
    else if(scrollPosition() < lastScroll && containHide()){
        //scroll up
        header.classList.remove('out123');
    }

    lastScroll = scrollPosition();
	})
	
	
	
	let lastScroll3 = 0;
    const defaultOffset2 = 100;
	const header2 = document.querySelector('.g-offcanvas-toggle');
	const scrollPosition2 = () => window.pageYOffset || document.documentElement.scrollTop;
    const containHide2 = () => header2.classList.contains('out123');
	
	
	window.addEventListener('scroll', () => {
    if(scrollPosition2() > lastScroll3 && !containHide2() && scrollPosition2() > defaultOffset) {
        //scroll down
        header2.classList.add('out123');
    }
    else if(scrollPosition2() < lastScroll3 && containHide2()){
        //scroll up
        header2.classList.remove('out123');
    }

    lastScroll3 = scrollPosition2();
	})
	
	let lastScroll4 = 0;
    const defaultOffset3 = 100;
	const header3 = document.querySelector('.main-notifications-wb');
	const scrollPosition3 = () => window.pageYOffset || document.documentElement.scrollTop;
    const containHide3 = () => header3.classList.contains('out123');
	
	
	window.addEventListener('scroll', () => {
    if(scrollPosition3() > lastScroll4 && !containHide3() && scrollPosition3() > defaultOffset) {
        //scroll down
        header3.classList.add('out123');
    }
    else if(scrollPosition3() < lastScroll4 && containHide3()){
        //scroll up
        header3.classList.remove('out123');
    }

    lastScroll4 = scrollPosition2();
	})
	
	*/
	

//function toggleColor1() {

//document.getElementById("joms-chat1").classList.toggle("nonedisp");
//document.getElementById("toolbarr").classList.toggle("nonedisp");

//}
/*******Появление и пропадание выбора список - плитка ********/
jQuery(document).ready(function(){
	  jQuery('#mytabs-module-contenttabs-127-1').toggle();
	 jQuery('.spisok').toggle();
jQuery("#mytabs-module-contenttabs-127-2").click(function() {
   jQuery("#ui-id-2").toggle(function() {
      jQuery('#mytabs-module-contenttabs-127-1').toggle();
      jQuery('#mytabs-module-contenttabs-127-2').toggle();
      jQuery('#ui-id-1').toggle();  
      jQuery('.sortgr').toggle();    
      //jQuery('#ui-id-2').fadeIn("slow");
     jQuery('.spisok').toggle();    
      //jQuery('.wqwqwq').fadeIn("slow");
   }); 
 });
 jQuery("#mytabs-module-contenttabs-127-1").click(function() {

   jQuery("#ui-id-2").toggle(function() {
      jQuery('#mytabs-module-contenttabs-127-1').toggle();
      jQuery('#mytabs-module-contenttabs-127-2').toggle();
      jQuery('#ui-id-1').toggle();  
      jQuery('.sortgr').toggle();    
      //jQuery('#ui-id-2').fadeIn("slow");
     jQuery('.spisok').toggle();    
      //jQuery('.wqwqwq').fadeIn("slow");
   }); 
 });
});

/*jQuery(document).ready(function($) {
$('.text-more').click(function(){
	// записываем предыдущий элемент
	var $prevText = $(this).prev();
	// добавим эффект, который обычно используется для анимации, но со скоростью 0 это будет незаметно
	$prevText.slideToggle(0);
	if ($('.text-next').is(":visible")) {
			$('.text-more').html('Свернуть');
			return false;
		} else {
			$('body,html').animate({scrollTop:120},300);
			$('.text-more').html('Подробнее');
		}		
	// т.к. это ссылка, необходимо сбросить переход по анкору
	
});
});	//JQuery не работает, переписал на JS
*/ 


	
jQuery(document).ready(function($) {//Появление строки поиска на нижней панели и закрытие

$('.joms-js--notification-search').click(function(){
$('.sl2-search').fadeIn(300);
$('#roksearch_results').css("display", "block");
		return false;
	});	
$('.closess').click(function(){
	$('.sl2-search').fadeOut(300);
		$('#roksearch_results').fadeOut(300);
		return false;
	});
});



/*


jQuery(document).ready(function($) {
$('.text-more').click(function(){
	// записываем предыдущий элемент
	var $prevText = $(this).prev();
	// добавим эффект, который обычно используется для анимации, но со скоростью 0 это будет незаметно
	$prevText.slideToggle(0);
	// т.к. это ссылка, необходимо сбросить переход по анкору
	return false;
});
});
	/* When the user clicks on the button,
toggle between hiding and showing the dropdown content 
function myFunction1() {
    document.getElementById("myDropdown1").classList.toggle("show1");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function() {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content1");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show1')) {
        openDropdown.classList.remove('show1');
      }
    }
  }
}




/* When the user clicks on the button,
toggle between hiding and showing the dropdown content 
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function() {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}*/
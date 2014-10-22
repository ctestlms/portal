function doMenu(item) {


         obj=document.getElementById(item);
         col=document.getElementById("x" + item);

         if (obj.style.display=="none") {
          obj.style.display="block";
          col.innerHTML="<img src='../course/arw_on.gif'>&nbsp;";

         }
         else {
          obj.style.display="none";
          col.innerHTML="<img src='../course/arw_off.gif'>&nbsp;";
         }
}
$(document).ready(function(){
	if($('.categorylist').length){
	//if($('.categorybox').length){
		$WWWROOT = "https://lms.nust.edu.pk/portal/";
		$last_active_state = readCookie("categories");
		$categories = $(".categorylist");
		$("div.categorylist:not(.level0)").css("display", "none");
		$('.indentation.level0').prepend('<img class="browsing-arrow" id="theImg" src="'+$WWWROOT+'pix/arw_off.gif" title="Show" /> ');
		$('.categorylist.level0 .category').prepend('<img class="browsing-arrow" id="theImg" src="'+$WWWROOT+'pix/arw_off.gif" title="Show"/>  ');
		$('.categorylist').addClass("inactive");
	
	
		if($last_active_state!=null){
			active_categories = $last_active_state.split(',');
			active_categories.pop();
			$(active_categories).each(function(){
				category = $(".categorylist:eq("+parseInt(this)+")");
				$self = category.attr("class").split(" ")[1];
				$level_no = parseInt($self .split("")[5]);
				if(getSiblings(category, $self , $level_no, false).length){
					category.find("div img").attr({"title": "Hide", "src": $WWWROOT+"pix/arw_on.gif"});
					getSiblings(category, $self,$level_no, false).slideDown(100);
				}else{
					category.find("div img").attr({"title": "Empty", "src": $WWWROOT+"pix/arw_on.gif"});
				}
			});
		}
	}
$("div.categorylist  img").click(function(){
	$category = $(this).parents("div.categorylist");
	$levels = $($category).attr("class").split(" ")[1];
	$level_no = parseInt($levels .split("")[5]);
	$next = $level_no+1;
	if(getSiblings($category,  $levels, $level_no, false).length){
		if($(this).attr("title") == "Hide"){
			$siblings = getSiblings($(this).parents("div.categorylist"), $levels, $level_no, true); //Get all siblings with next level class;
			$(this).attr({"title": "Show", "src": $WWWROOT+"pix/arw_off.gif"});
			$siblings.find("div.indentation.level0 img").attr({ "title": "Show","src": $WWWROOT+"pix/arw_off.gif"});
			$siblings.slideUp(700);
		}else{
			$(this).attr({ "title": "Hide","src": $WWWROOT+"pix/arw_on.gif"});
			$my_siblings = getSiblings($(this).parents("div.categorylist"), $levels, $level_no, false);
			$my_siblings.slideDown(300);
		}
	}else{
	$(this).attr({"title": "Empty", "src": $WWWROOT+"pix/arw_on.gif"});
	}
	
	$visible = $("img[title=Hide], img[title=Empty]");
	if($visible.length){
		$index = "";
		$($visible).each(function(){
			$index += $(".categorylist").index($(this).parents(".categorylist"))+",";
			
	});
	createCookie('categories', $index, "");
	}

	
	});
});


function getSiblings(category, level, level_no, flag){
	var $sibs = [];
	$next_level = "level"+(parseInt(level_no)+1);
	$check=1;
	category.nextAll("div.categorylist").each( function() {
		$this_level = $(this).attr("class").split(" ")[1];
		$this_level_no = parseInt($this_level.split("")[5]);
		if($this_level_no <= level_no)
			$check=0;
		if(($(this).hasClass($next_level) || flag) && $check==1)
			$sibs.push(this);
		    });
		return $($sibs);
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}
//Function to get Cookie
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}






$(document).ready(function(){
	var q =  $(".side-fixed"),
		qBtn = q.find(".side-btn-close"), //닫기 버튼
		qBtnImg = qBtn.find("img"),
		qBtnSrc = qBtnImg.attr("src"),
		qWidth = 204; // 퀵바 가로(width)

	qBtn.click(function(){
		var qRight = q.css("right");

		// 닫혔을때
		if (qRight == "0px"){
			q.stop(true,true).animate({"right":-qWidth});
			qBtnImg.attr("src",qBtnSrc.replace("_close","_open"));	

			Cookies.set("checks", "ucks",{ expires: 1, path: "/" });

		// 열렸을때
		} else if (qRight == -qWidth+"px") {
			q.stop(true,true).animate({"right":0});
			qBtnImg.attr("src",qBtnSrc.replace("_open","_close"));

			Cookies.remove("checks",{ path: "/" });
		}
	});	
	if (Cookies.get("checks") == "ucks"){
		q.css({"right":-qWidth});
		qBtnImg.attr("src",qBtnSrc.replace("_close","_open"));
	} else {			
		q.css({"right":0});
		qBtnImg.attr("src",qBtnSrc.replace("_open","_close"));
	};
});

/* 즐겨찾기 */				
function bookmarksite(title,url) {				
	// Internet Explorer			
	var agent = navigator.userAgent.toLowerCase();			
	if ( (navigator.appName == 'Netscape' && agent.indexOf('trident') != -1) || (agent.indexOf("msie") != -1) ) {			
		window.external.AddFavorite(url, title);		
	}			
	else if(agent.indexOf("chrome") != -1) {			
		alert("Ctrl+D키를 누르시면 즐겨찾기에 추가하실 수 있습니다.");		
	}			
	else if(agent.indexOf("firefox") != -1) {			
		window.sidebar.addPanel(title, url, "");		
	}			
	else if(agent.indexOf("opera") != -1) {			
		var elem = document.createElement('a');		
		elem.setAttribute('href',url);		
		elem.setAttribute('title',title);		
		elem.setAttribute('rel','sidebar');		
		elem.click();		
	}			
}	
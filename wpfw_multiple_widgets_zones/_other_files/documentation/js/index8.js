$(document).on("animating.slides", function(a){
	// Special fix if we want to display FitText headings
	setTimeout(function(){
		$("h1.fittext").fitText(1, { minFontSize: "50px", maxFontSize: "100px" });
		$("h2.fittext").fitText(1, { minFontSize: "40px", maxFontSize: "80px" });
		$("h3.fittext").fitText(1, { minFontSize: "30px", maxFontSize: "60px" });
		$("h4.fittext").fitText(1, { minFontSize: "20px", maxFontSize: "40px" });
		$("h5.fittext").fitText(1, { minFontSize: "15px", maxFontSize: "30px" });
		$("h6.fittext").fitText(1, { minFontSize: "10px", maxFontSize: "20px" });
	}, 100)
});

// Fullscreen content and background slider / fader
$("#slides").superslides({
	// play: 6000,
	animation: "fade", // Choose between slide or fade
	pagination: true // Choose between true or false
});
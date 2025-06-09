(() => {
	if (!location.href.startsWith(NeoHPSite)) {
		// 全要素に対して背景と文字を黒に
		document.querySelectorAll('*').forEach(el => {
			el.style.backgroundColor = 'black';
			el.style.color = 'black';
		});

		// 指定されたタグを非表示に
		document.querySelectorAll('div,svg,canvas,img,video,audio,iframe').forEach(el => {
			el.style.display = 'none';
		});
	}
})();

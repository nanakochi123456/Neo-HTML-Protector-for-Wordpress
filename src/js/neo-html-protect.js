(function($) {
	var	kd='keydown',
		FlagAll=NeoHPFlg,
		FlagSmall=FlagAll.toLowerCase();
		CT='Ctrl+',
		SF='Shift+',
		unixTime = Math.floor(Date.now() / 1000);

	// キーのみの処理
	document.addEventListener(kd, (event) => {
		var ctrl = event.ctrlKey,
			shift = event.shiftKey,
			key = event.key;

		// F12
		if(FlagSmall.includes('f')) {
			if (key === 'F12') {
				sendIpToServer('F12', 'F');
				event.preventDefault();
			}
		}

		// Ctrl+Shift+I
		if(FlagSmall.includes('i')) {
			if (ctrl && shift && (key === 'I' || key === 'i')) {
				sendIpToServer(CT+SF+'I', 'I');
				event.preventDefault();
			}
		}

		// Ctrl+Shift+J
		if(FlagSmall.includes('j')) {
			if (ctrl && shift && (key === 'J' || key === 'j')) {
				sendIpToServer(CT+SF+'J', 'J');
				event.preventDefault();
			}
		}

		// Ctrl+U
		if(FlagSmall.includes('u')) {
			if (ctrl && (key === 'U' || key === 'u')) {
				sendIpToServer(CT+'U', 'U');
				event.preventDefault();
			}
		}

		// Ctrl+P
		if(FlagSmall.includes('p')) {
			if (ctrl && (key === 'P' || key === 'p')) {
				sendIpToServer(CT+'P', 'P');
				e.preventDefault();
			}
		}
	});

	// 右クリック
	if(FlagSmall.includes('r')) {
		$(document).on('contextmenu', function(e) {
			sendIpToServer('Right Click', 'R');
			e.preventDefault();
		});
	}

	// テキスト選択禁止のみ 通知なし
	if(FlagSmall.includes('s')) {
		$(document).on('selectstart', function(e) {
			e.preventDefault();
		});
	}

	// デバッガ妨害
	if(FlagSmall.includes('d')) {
		setInterval(function() {
			console.clear();
			debugger;
		}, 100);
	}

	// IPアドレスをサーバーに送信
	function sendIpToServer(Keys, Flg) {
		$.ajax({
			url: NeoHPHome + "?neohp=ajax&amp;tm=" + unixTime,
			type: 'POST',
			data: {
				sec: 'papu',
				url: location.href,
				key: Keys,
			},
			success: function(response) {
				// alertで表示後URL転送
				if(FlagAll.includes(Flg)) {
					alert(escapeHtml(response));
					location.href = NeoHPHome + "?neohp=redirect&amp;tm=" + unixTime;
				}
			}
		});
	}

	// JavaScriptでエスケープする関数
	function escapeHtml(str) {
		var e = document.createElement('div');
		if (str) {
			e.innerText = str;
			e.textContent = str;
		}
		return e.innerHTML;
	}
})(jQuery);

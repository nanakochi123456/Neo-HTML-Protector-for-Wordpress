/**
 * Neo HTML Protector
 */

/** @suppress {undefinedVars} */

(function($) {
	const	keydown='keydown';
	const	CtrlKey='Ctrl+';
	const	ShiftKey='Shift+';
	const	fKey='f';
	const	iKey='i';
	const	jKey='j';
	const	uKey='u';
	const	pKey='p';
	const	sKey='s';
	const	rClick='r';
	const	CopyCut='c';

	let		FlagAll=NeoHPFlg;
	let		FlagSmall=lower(FlagAll);
	let		Nonce=NeoHPnonce;
	let		unixTime = Math.floor(Date.now() / 1000);

	function lower(str) {
		return str.toLowerCase();
	}

	function upper(str) {
		return str.toUpperCase();
	}

	// キーのみの処理
	document.addEventListener(keydown, (event) => {
		var ctrl = event.ctrlKey,
			shift = event.shiftKey,
			key = event.key;

		// F12
		if(FlagSmall.includes(fKey)) {
			if (key === 'F12') {
				sendIpToServer('F12', fKey);
				event.preventDefault();
			}
		}

		// Ctrl+Shift+I
		if(FlagSmall.includes(iKey)) {
			if (ctrl && shift && lower(key) === iKey) {
				sendIpToServer(CtrlKey+ShiftKey+upper(iKey), iKey);
				event.preventDefault();
			}
		}

		// Ctrl+Shift+J
		if(FlagSmall.includes(jKey)) {
			if (ctrl && shift && lower(key) === 'j') {
				sendIpToServer(CtrlKey+ShiftKey+upper(jKey), jKey);
				event.preventDefault();
			}
		}

		// Ctrl+U
		if(FlagSmall.includes(uKey)) {
			if (ctrl && lower(key) === uKey) {
				sendIpToServer(CtrlKey+upper(uKey), uKey);
				event.preventDefault();
			}
		}

		// Ctrl+P
		if(FlagSmall.includes(pKey)) {
			if (ctrl && lower(key) === pKey) {
				sendIpToServer(CtrlKey+upper(pKey), pKey);
				event.preventDefault();
			}
		}

		// Ctrl+S
		if(FlagSmall.includes(sKey)) {
			if (ctrl && lower(key) === sKey) {
				sendIpToServer(CtrlKey+upper(sKey), sKey);
				event.preventDefault();
			}
		}
	});

	// 右クリック
	if(FlagSmall.includes(rClick)) {
		$(document).on('contextmenu', (event)=> {
			sendIpToServer('Right Click', rClick);
			event.preventDefault();
		});
	}

	// copy, cut
	if(FlagSmall.includes(CopyCut)) {
		document.addEventListener('copy', (event) => {
			sendIpToServer('Copy', CopyCut);
			event.preventDefault();
		});

		document.addEventListener('cut', (event) => {
			sendIpToServer('Cut', CopyCut);
			event.preventDefault();
		});
	}

	// テキスト選択禁止のみ 通知なし
	if(FlagSmall.includes('t')) {
		document.body.style.userSelect = 'none';
		document.body.style.webkitUserSelect = 'none'; // Safari対策
		//document.body.style.msUserSelect = 'none'; // 古いIE対策 いらないのでコメントアウト

		$(document).on('selectstart', (event) => {
			event.preventDefault();
		});

		document.addEventListener('touchstart', (event) => {
			event.preventDefault();
		});

		document.addEventListener('touchmove', (event) => {
			event.preventDefault();
		});

		document.addEventListener('touchend', (event) => {
			event.preventDefault();
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
		Flg=upper(Flg);

		$.ajax({
			url: NeoHPHome
				+ "?neohp=ajax&tm=" + unixTime
				+ "&neononce=" + Nonce,
			type: 'POST',
			data: {
				sec: 'papu',
				url: location.href,
				key: Keys,
			},
			success: function(response) {
				// alertで表示後URL転送
				if(FlagAll.includes(Flg)) {
					alert(escapeHtml(response.replace(/\\n/g, '\n')));
					location.href
						= NeoHPHome
						+ "?neohp=redirect"
						+ "&tm=" + unixTime
						+ "&neononce=" + Nonce;
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

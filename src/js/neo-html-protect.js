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
	const	zKey='z';
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

	/////////////////////////////////////////////////
	// キーやマウスクリック等のイベント処理

	// 動作を停止する
	function stop(event) {
		event.preventDefault();
		event.stopPropagation();
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
				stop(event);
			}
		}

		// Ctrl+Shift+I
		if(FlagSmall.includes(iKey)) {
			if (ctrl && shift && lower(key) === iKey) {
				sendIpToServer(CtrlKey+ShiftKey+upper(iKey), iKey);
				stop(event);
			}
		}

		// Ctrl+Shift+J
		if(FlagSmall.includes(jKey)) {
			if (ctrl && shift && lower(key) === 'j') {
				sendIpToServer(CtrlKey+ShiftKey+upper(jKey), jKey);
				stop(event);
			}
		}

		// Ctrl+U
		if(FlagSmall.includes(uKey)) {
			if (ctrl && lower(key) === uKey) {
				sendIpToServer(CtrlKey+upper(uKey), uKey);
				stop(event);
			}
		}

		// Ctrl+P
		if(FlagSmall.includes(pKey)) {
			if (ctrl && lower(key) === pKey) {
				sendIpToServer(CtrlKey+upper(pKey), pKey);
				stop(event);
			}
		}

		// Ctrl+S
		if(FlagSmall.includes(sKey)) {
			if (ctrl && lower(key) === sKey) {
				sendIpToServer(CtrlKey+upper(sKey), sKey);
				stop(event);
			}
		}
	});

	// 右クリック
	if(FlagSmall.includes(rClick)) {
		// mousedown mouseup dragstart
		$(document).on('contextmenu', (event)=> {
			sendIpToServer('Right Click', rClick);
			stop(event);
		});
	}

	// copy, cut
	if(FlagSmall.includes(CopyCut)) {
		document.addEventListener('copy', (event) => {
			sendIpToServer('Copy Cut', CopyCut);
			stop(event);
		});
	}

	// テキスト選択禁止のみ 通知なし
	if(FlagSmall.includes('t')) {
		document.body.style.userSelect = 'none';
		document.body.style.webkitUserSelect = 'none'; // Safari対策
		//document.body.style.msUserSelect = 'none'; // 古いIE対策 いらないのでコメントアウト

		$(document).on('selectstart touchstart touchmove touchend', (event) => {
			stop(event);
		});
	}

	// デバッガ妨害
	if(FlagSmall.includes('d')) {
		// 1個目 コンソールクリア＆debuggerコマンド実行
		setInterval(function() {
			console.clear();
			debugger;
		}, 100);

		// 2個目 debuggerコマンドを無効化する

		Object.defineProperty(window, 'debugger', {
			set: function() {},
			get: function() {}
		});

		// 3個目 console.logを無効化する

		console.log = function() {};

		// 4個目 デバッガーが開かれたかチェックする
		// だめなのでコメントアウト
/*
		var isDebuggerActive = false;
		setInterval(function() {
			if (new Date() - performance.timing.navigationStart < 500) {
				alert("debugger open");
			}
		}, 100);
*/

		// 5個目 開発者ツールの開閉をチェックする
		// だめなのでコメントアウト
/*
		var lastTime = 0;
		setInterval(function() {
			var now = new Date();
			if (now - lastTime > 5000) {
				lastTime = now;
			} else {
				alert('Debugger opened!');
			}
		}, 100);
*/
		// 6個目 console.debugを無効化する

		console.debug = function() {};


		// 7個目 window.onerror をカスタマイズしてデバッガの検出を試みる
		// 意味ないのでコメントアウト
/*
		window.onerror = function(message, source, lineno, colno, error) {
			if (error.stack.indexOf('Debugger') !== -1) {
				alert('Debugger detected!');
			}
		};
*/

		// 8個目 開発者ツールの起動を検出 デバッガや開発者ツールが開かれたことを検出して、警告を表示する
		// だめなのでコメントアウト
/*
		var lastTime = new Date();
		setInterval(function() {
			if (new Date() - lastTime > 1000) {
				lastTime = new Date();
			} else {
				alert("開発者ツールが開かれています");
			}
		}, 100);
*/
		// 9個目 スクリーンショットの防止（全画面時のみ）

		document.documentElement.requestFullscreen();

		// 10個目 debuggerコマンドを使ったデバッガの無効化

		Object.defineProperty(window, 'debugger', {
			set: function() {},
			get: function() {}
		});

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

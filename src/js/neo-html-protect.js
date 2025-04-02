/**
 * Neo HTML Protector
 */

/** @suppress {undefinedVars} */

//(function($) {
(($) => {
	"use strict";

	let		CtrlKey='Ctrl+';
	let		ShiftKey='Shift+';
	let		WinKey='Win+';
	let		AltKey='Alt+';
	let		CommandKey='Command+';
	let		PrintScreenKey='PrintScreen';
	let		fKey='f';
	let		iKey='i';
	let		jKey='j';
	let		uKey='u';
	let		pKey='p';
	let		sKey='s';
	let		zKey='z';
	let		rClick='r';
	let		CopyCut='c';
	let		PrintScreen='a';
	let		none='none';
	let		undefined='undefined';
	let		Document=document;
	let		Window=window;
	let		body='body';
	let		black='black';
	let		nullstr = '';
	let		px20='20px';
	let		ua=navigator.userAgent;

	let		FlagAll=NeoHPFlg;
	let		FlagSmall=lower(FlagAll);
	let		Nonce=NeoHPnonce;
	let		unixTime = Math.floor(Date.now() / 1000);

	let		Cryptojs;

	if (typeof CryptoJS !== undefined) {
		Cryptojs = CryptoJS;
	} else {
		Cryptojs = undefined;
	}

	nullstr = nullstr+ nullstr;
	CtrlKey			+= nullstr;
	ShiftKey		+= nullstr;
	AltKey			+= nullstr;
	WinKey			+= nullstr;
	CommandKey		+= nullstr;
	fKey			+= nullstr;
	iKey			+= nullstr;
	jKey			+= nullstr;
	uKey			+= nullstr;
	pKey			+= nullstr;
	sKey			+= nullstr;
	zKey			+= nullstr;
	PrintScreen		+= nullstr;
	PrintScreenKey	+= nullstr;
	rClick			+= nullstr;
	CopyCut			+= nullstr;
	none			+= nullstr;
	body			+= nullstr;
	black			+= nullstr;
	undefined		+= nullstr;
	px20			+= nullstr;
	ua				+= nullstr;

	function lower(str) {
		return str.toLowerCase() + nullstr;
	}

	function upper(str) {
		return str.toUpperCase() + nullstr;
	}

	/////////////////////////////////////////////////
	// 暗号化されたURLを復号化する関数

	function urlSafeBase64Decode(str) {
		let base64 = str.replace(/-/g, '+').replace(/_/g, '/').replace(/\./g, '=');
		// こちらのが低速らしい
		// let base64 = str.replace(/[-_.]/g, char => ({'-': '+', '_': '/', '.': '='})[char]);
		return atob(swapCase(base64));
	}

	// どうせなら大文字と小文字を入れ替える
	function swapCase(str) {
		return str.replace(/[a-zA-Z]/g, (char) => {
			return char === upper(char)
				? lower(char)
				: upper(char);
		});
	}

	// 暗号化した画像URLを復号化
	async function decryptAndDecodeImageUrl(encryptedData, nonce) {
		if (typeof Cryptojs !== undefined) {
			return new Promise((resolve, reject) => {
				try {
					// Base64デコードして暗号化されたデータとIVを分ける
					let decodedData = urlSafeBase64Decode(encryptedData);
					let parts = decodedData.split(':');

					let encryptedUrl = parts[0];
					let encodedIv = parts[1];

					// IVをBase64デコード
					let iv = Cryptojs.enc.Base64.parse(encodedIv);

					// nonceをキーとしてSHA256で生成（WordArray型）
					let key = Cryptojs.SHA256(nonce);

					// 暗号化データをBase64からパース
					let encryptedWordArray = Cryptojs.enc.Base64.parse(encryptedUrl);

					// AESで復号化
					let decrypted = Cryptojs.AES.decrypt(
						{ ciphertext: encryptedWordArray },
						key,
						{
							iv: iv,
							mode: Cryptojs.mode.CBC,
							padding: Cryptojs.pad.Pkcs7
						}
					);

					// 復号化したURLを文字列に変換
					let decryptedUrl = decrypted.toString(Cryptojs.enc.Utf8);

					if (ua.includes('Safari') && !ua.includes('Chrome') ) {
						setTimeout(() => {
							// decryption logic...
							resolve(decryptedUrl); // 非同期で結果を返す
						}, 20); // 少し遅延を加える
					} else {
						resolve(decryptedUrl);
					}
				} catch (error) {
					// reject(error);
				}
			});
	  	}
	}

	// 例: 画像のdata-src属性を取得して復号化する lazyロード
	if (FlagAll.includes(upper(zKey))) {
		Window.onload = async function () {  // window.onload を async 関数に変更
			if (typeof Cryptojs !== undefined) {
				// IntersectionObserverを使ってlazyロードを実現
				let imgTags = Document.querySelectorAll('img[data-src]');

				// IntersectionObserverのコールバック関数
				let observer = new IntersectionObserver(async (entries, observer) => {
					for (let entry of entries) {
						if (entry.isIntersecting) {
							let img = entry.target;
							if (img.className.includes('protected')) {
								let encryptedData_src = img.getAttribute('data-src');
								let encryptedData_srcset = img.getAttribute('data-srcset');
								let nonce = img.getAttribute('data-nonce');

								try {
									// 画像URLを非同期で復号化
									let decryptedUrl_src = await decryptAndDecodeImageUrl(encryptedData_src, nonce);
									let decryptedUrl_srcset = encryptedData_srcset
										? await decryptAndDecodeImageUrl(encryptedData_srcset, nonce)
										: null;

									// 復号化したURLを元のsrcに設定
									img.src = decryptedUrl_src;

									if (decryptedUrl_srcset !== undefined) {
										img.srcset = decryptedUrl_srcset;
									}

									// 読み込んだ画像は監視から外す
									observer.unobserve(img);
								} catch (error) {
									//console.error('Decryption failed:', error);
								}
							}
						}
					}
				}, {
					root: null, // ビューポートが基準
					rootMargin: '0px', // ビューポートの周囲のマージン
					threshold: 0.1 // 画像が10%表示されたらロード
				});

				// 画像が表示されるまで監視を開始
				imgTags.forEach(function (img) {
					observer.observe(img);
				});
			}
		};
	}

/*
	// 例: 画像のdata-src属性を取得して復号化する 非lazyロード
	if(FlagAll.includes(zKey)) {
//		Document.addEventListener('DOMContentLoaded', function() {
		$(Document).ready(function() {
			if (Cryptojs !== undefined) {
				// すべての img[data-src] タグを取得
				let imgTags = Document.querySelectorAll('img[data-src]');
				
				imgTags.forEach(img => {
					if (img.className.includes('protected')) {
						let encryptedData_src = img.getAttribute('data-src');
						let encryptedData_srcset = img.getAttribute('data-srcset');
						let nonce = img.getAttribute('data-nonce');
						
						// 画像URLを復号化
						let decryptedUrl_src = await decryptAndDecodeImageUrl(encryptedData_src, nonce);
						let decryptedUrl_srcset = await decryptAndDecodeImageUrl(encryptedData_srcset, nonce);
						
						// 復号化したURLを元のsrcに設定
						img.src = decryptedUrl_src;

						if (decryptedUrl_srcset !== undefined) {
							img.srcset = decryptedUrl_srcset;
						}
					}
				});
			}
		});
	}
*/
	/////////////////////////////////////////////////
	// キーやマウスクリック等のイベント処理

	// 動作を停止する
	function stop(event) {
		event.preventDefault();
		event.stopPropagation();
	}

	// キーのみの処理
	$(Document).on("keydown keypress keyup", (event) => {
		var ctrl = event.ctrlKey,
			shift = event.shiftKey,
			alt = event.altKey,
			meta = event.metaKey,
			key = event.key,
			code = event.keyCode;

		// PrintScreen
		if(FlagSmall.includes(PrintScreen)) {
			if (shift && code === 44) {
				sendIpToServer(ShiftKey + PrintScreenKey, PrintScreen);
				stop(event);
			}

			if (alt && code === 44) {
				sendIpToServer(AltKey + PrintScreenKey, PrintScreen);
				stop(event);
			}

			if (code === 44) {
				sendIpToServer(PrintScreenKey, PrintScreen);
				stop(event);
			}

			// Win+Shift+S (Windows sniping tool)
			if(ua.includes("Windows")) {
				if (meta && shift && lower(event.key) === 's') {
					sendIpToServer(WinKey + ShiftKey+ 'S', fKey);
					stop(event);
				}

				// Win+Alt+R (Game Bar)
				if (meta && alt && lower(event.key) === 'r') {
					sendIpToServer(WinKey + AltKey + 'R', fKey);
					stop(event);
				}

				// Win+G (Game Bar)
				if (meta && lower(event.key) === 'g') {
					sendIpToServer(WinKey + 'G', fKey);
					stop(event);
				}
			}

			if(ua.includes("Macintosh")) {
				// Shift+Command+3 (macos)
				if (meta && shift && key === '3') {
					sendIpToServer(ShiftKey + CommandKey + '3', fKey);
					stop(event);
				}

				// Shift+Command+4 (macos)
				if (meta && shift && key === '4') {
					sendIpToServer(ShiftKey + CommandKey + '4', fKey);
					stop(event);
				}
			}

			// Ctrl+Shift+P (chrome os)
			if(ua.includes("CrOS")) {
				if (ctrl && shift && lower(key) === 'p') {
					sendIpToServer(CtrlKey + ShiftKey + 'P', fKey);
					stop(event);
				}

				// Ctrl+Shift+F5 (chrome os)
				if (ctrl && shift && lower(key) === 'f5') {
					sendIpToServer(CtrlKey + ShiftKey + 'F5', fKey);
					stop(event);
				}

				// Ctrl+F5 (chrome os)
				if (ctrl && lower(key) === 'f5') {
					sendIpToServer(CtrlKey + 'F5', fKey);
					stop(event);
				}
			}
		}

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
			if (ctrl && shift && lower(key) === jKey) {
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
			// @media print{body{display:none !important}} 相当
			let mediaQuery = Window.matchMedia('print');

			mediaQuery.addEventListener('change', (e) => {
				if (e.matches) {
					Document.body.style.display = none;
				}
			});

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
		$(Document).on('contextmenu', (event) => {
			sendIpToServer('Right Click', rClick);
			stop(event);
		});
	}

	// copy, cut
	if(FlagSmall.includes(CopyCut)) {
		$(Document).on('copy', (event) => {
			sendIpToServer('Copy Cut', CopyCut);
			stop(event);
		});
	}

	// テキスト選択禁止のみ 通知なし
	if(FlagSmall.includes('t')) {
		$(body).css({
			'user-select': none,
			'-webkit-user-select': none, // Safari, Chrome
			'-moz-user-select': none,	 // Firefox
			'-ms-user-select': none 	 // Internet Explorer, Edge
		});

		Document.querySelectorAll('img').forEach(img => {
//		$('img').each(function() {
			img.style.pointerEvents = none;
		});

		$(Document).on('selectstart touchmove', (event) => {
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

		Object.defineProperty(Window, 'debugger', {
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

		Document.documentElement.requestFullscreen();

		// 10個目 debuggerコマンドを使ったデバッガの無効化

		Object.defineProperty(Window, 'debugger', {
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
				+ "&neohpnonce=" + Nonce,
			type: 'POST',
			data: {
				sec: none,
				url: location.href,
				key: Keys,
			},
			success: function(response) {
				// alertで表示後URL転送
				if(FlagAll.includes(Flg)) {
					// 全部真っ黒にする
					$('*').css({
						'background-color' : black,
						'color' : black,
					});

					$('img,video,audio,iframe').css({
						'display' : none
					});
					var text=escapeHTMLWithBr( response.replace(/\\n/g, '<br>') );
					var time=NeoHPTime;

					var newDiv = $('<div>')
						.html(text) // 文字を設定
						.css({
							'position': 'fixed',			// 画面上で固定
							'top': '50%',					// 画面の中央に配置
							'left': '50%',
							'transform': 'translate(-50%,-50%)', // 中央揃え
							'background-color': '#ff0',	// 背景色を黄色に
							'color': black,				 	// 文字色を黒に
							'padding': px20,				// パディング
							'border-radius': px20,		// 角を丸く
							'z-index': '9999',				// 最前面に表示
							'font-weight': 'bold'			// 文字を太字に
						});

					// body に追加
					$(body).append(newDiv);

					if(time > 0) {
						setTimeout(() => {
							location.href = NeoHPHome
											+ "?neohp=redirect"
											+ "&page=" + encodeURIComponent(NeoHPPage)
											+ "&tm=" + unixTime
											+ "&neononce=" + Nonce;
						}, time * 1000);  // 5000ミリ秒 = 5秒
					}
				}
			}
		});
	}

	// JavaScriptでエスケープする関数
	function escapeHtml(str) {
		var e = Document.createElement('div');
		if (str) {
			e.innerText = str;
			e.textContent = str;
		}
		return e.innerHTML;
	}

	function escapeHTMLWithBr(str) {
		return str
			.replace(/<br\s*\/?>/gi, '__BR__')	// <br> を一時的に置き換え
			.replace(/&/g, '&amp;') 			// & をエスケープ
			.replace(/</g, '&lt;')				// < をエスケープ
			.replace(/>/g, '&gt;')				// > をエスケープ
			.replace(/"/g, '&quot;')            // " をエスケープ
			.replace(/'/g, '&#039;')            // ' をエスケープ
			.replace(/__BR__/g, '<br>');		// <br> を元に戻す
	}
})(jQuery);

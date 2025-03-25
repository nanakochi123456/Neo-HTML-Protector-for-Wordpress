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

	/////////////////////////////////////////////////
	// 暗号化されたURLを復号化する関数

	function urlSafeBase64Decode(str) {
		let base64 = str.replace(/-/g, '+').replace(/_/g, '/').replace(/\./g, '=');
	//	  return atob(base64);
		return atob(swapCase(base64));
	}

	// どうせなら大文字と小文字を入れ替える
	function swapCase(str) {
		return str.replace(/[a-zA-Z]/g, function(char) {
			return char === upper(char)
				? lower(char)
				: upper(char);
		});
	}

	function decryptAndDecodeImageUrl(encryptedData, nonce) {
		try {
			// Base64デコードして暗号化されたデータとIVを分ける

			const decodedData = urlSafeBase64Decode(encryptedData);
			const parts = decodedData.split('::');

			if (parts.length !== 2) {
				console.error("Invalid encrypted data format");
				return null;
			}

			const encryptedUrl = parts[0];
			const encodedIv = parts[1];

			// IVをBase64デコード
			const iv = CryptoJS.enc.Base64.parse(encodedIv);

			// nonceをキーとしてSHA256で生成（WordArray型）
			const key = CryptoJS.SHA256(nonce);

			// 暗号化データをBase64からパース
			const encryptedWordArray = CryptoJS.enc.Base64.parse(encryptedUrl);

			// AESで復号化
			const decrypted = CryptoJS.AES.decrypt(
				{ ciphertext: encryptedWordArray },
				key,
				{
					iv: iv,
					mode: CryptoJS.mode.CBC,
					padding: CryptoJS.pad.Pkcs7
				}
			);

			if (!decrypted) {
				console.error("Decryption failed");
				return null;
			}

			// 復号化したURLを文字列に変換
			const decryptedUrl = decrypted.toString(CryptoJS.enc.Utf8);
			
			if (!decryptedUrl) {
				console.error("Decryption result is empty");
				return null;
			}

			return decryptedUrl;
		} catch (error) {
			console.error("Decryption failed:", error);
			return null;
		}
	}


	// 例: 画像のdata-src属性を取得して復号化する lazyロード

	document.addEventListener('DOMContentLoaded', function() {
		// IntersectionObserverを使ってlazyロードを実現
		const imgTags = document.querySelectorAll('img[data-src]');

		// IntersectionObserverのコールバック関数
		const observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const img = entry.target;
					const encryptedData = img.getAttribute('data-src');
					const nonce = img.getAttribute('data-nonce');
					
					// 画像URLを復号化
					const decryptedUrl = decryptAndDecodeImageUrl(encryptedData, nonce);
					
					// 復号化したURLを元のsrcに設定
					img.src = decryptedUrl;

					// 読み込んだ画像は監視から外す
					observer.unobserve(img);
				}
			});
		}, {
			root: null, // ビューポートが基準
			rootMargin: '0px', // ビューポートの周囲のマージン
			threshold: 0.1 // 画像が10%表示されたらロード
		});

		// 画像が表示されるまで監視を開始
		imgTags.forEach(function(img) {
			observer.observe(img);
		});
	});

/*
// 例: 画像のdata-src属性を取得して復号化する 非lazyロード
	document.addEventListener('DOMContentLoaded', function() {
		const imgTags = document.querySelectorAll('img[data-src]');
		imgTags.forEach(function(img) {
			const encryptedData = img.getAttribute('data-src');
			const nonce = img.getAttribute('data-nonce');
			
			// 画像URLを復号化
			const decryptedUrl = decryptAndDecodeImageUrl(encryptedData, nonce);
			
			// 復号化したURLを元のsrcに設定
			img.src = decryptedUrl;
		});
	});

*/

})(jQuery);

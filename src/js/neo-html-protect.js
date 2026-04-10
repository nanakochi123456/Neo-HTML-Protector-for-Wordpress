/*! Neo HTML Protector 0.4.6 */

/** @suppress {undefinedVars} */

//(function($) {
(($) => {
	"use strict";

	let		nullstr = '';
	nullstr += nullstr;
	let		slash = '/'	+ nullstr;

	let		pKey='p'	+ nullstr;
	let		zKey='z'	+ nullstr;
	let		wKey='w'	+ nullstr;
	let		rClick='r'	+ nullstr;
	let		CopyCut='y'	+ nullstr;
	let		none='none'	+ nullstr;
	let		undefined='undefined'	+ nullstr;
	let		Document=document;
	let		Window=window;
	let		Navigator=navigator
	let		body='body'	+ nullstr;
	let		div='<div>'	+ nullstr;
	let		blur='blur'	+ nullstr;
	let		fixed='fixed'	+ nullstr;

	let		ua=nullstr;

	let		current_url = location.href;

	let		Windows = 'Windows'	+ nullstr;
	let		macOS = 'macOS'		+ nullstr;
	let		ChromeOS = 'Chrome OS'	+ nullstr;
	let		Android = 'Android'	+ nullstr;
	let		AndroidTab = Android + 'Tab'+ nullstr;
	let		iOS = 'iOS'			+ nullstr;
	let		ipadOS = 'iPadOS'	+ nullstr;
	let		Chrome='Chrome'		+ nullstr;
	let		Safari='Safari'		+ nullstr;
	let		Firefox='Firefox'	+ nullstr;
	let		Sleipnir='Sleipnir'	+ nullstr;

	let		EDG="EDG"			+ slash;

	let		FlagAll=NeoHPFlg;
	let		FlagSmall=lower(FlagAll);
	let		Nonce=NeoHPnonce;
	let		unixTime = Math.floor(Date.now() / 1000);


	let		datastr = 'data-' + nullstr;
	let		srcstr = 'src' + nullstr;
	let		srcsetstr = srcstr + 'set';
	let		noncestr = 'nonce' + nullstr;

	let	CookieName = "neoagree"	+ nullstr;

	let		eventflag = 0;
	let		Cryptojs;
	let		lastBlur = 0;

	// BEEPを鳴らすためのコンテキスト
	let ctx = new (Window.AudioContext || Window.webkitAudioContext)();

	// BEEPを連続で鳴らしすぎないためのトリガー変数
	let lastTrigger = 0;

	if (typeof CryptoJS !== undefined) {
		Cryptojs = CryptoJS;
	} else {
		Cryptojs = undefined;
	}

	async function detectBrowserAndOS() {
		let ua = Navigator.userAgent;
		let	ualower=lower(ua);
		let browser = nullstr;
		let os = nullstr;

		let		Edge='Edge'			+ nullstr;
		let		Opera='Opera'		+ nullstr;
		let		Vivaldi='Vivaldi'	+ nullstr;
		let		B='Browser'			+ nullstr;
		let		Avast='Avast'		+ nullstr;
		let		SamsungBrowser='Samsung'+ B;
		let		UCBrowser='UC'		+ B;
		let		QQBrowser='QQ'		+ B;
		let		Puffin='Puffin'		+ nullstr;
		let		CocCoc='CocCoc'		+ nullstr;
		let		Maxthon='Maxthon'	+ nullstr;
		let		Dolphin='Dolphin'	+ nullstr;
		let		SeaMonkey='SeaMonkey'+nullstr;
		let		Konqueror='Konqueror'+nullstr;

		let		OPR="OPR"			+ slash;

		let		Nintendo = 'Nintendo'	+ nullstr;
		let		PlayStation = 'PlayStation'	+ nullstr;
		let		Xbox = 'Xbox'		+ nullstr;
		let		FireTab = 'FireTab'	+ nullstr;
		let		Linux = 'Linux'		+ nullstr;

		let		uaVersion = nullstr;
		let		uaData;

		// Braveなら別の取得方式
		let	isBrave = Navigator.brave && await Navigator.brave.isBrave();

		// ブラウザ名の判定（userAgentData対応時）
		if (Navigator.userAgentData) {
			const brands = Navigator.userAgentData.brands || [];
			for (const brand of brands) {
				if (brand.brand.includes(Chrome)) browser = Chrome;
				else if (brand.brand.includes(Opera)) browser = Opera;
				else if (brand.brand.includes(Edge)) browser = Edge;
				else if (brand.brand.includes(Firefox)) browser = Firefox;
				else if (brand.brand.includes(Safari) && browser === nullstr) browser = Safari;

				uaData = await navigator.userAgentData.getHighEntropyValues(["uaFullVersion"]);
				uaVersion = uaData.uaFullVersion;
			}

			// OS の高精度取得
			uaData = await Navigator.userAgentData.getHighEntropyValues(["platform"]);
			// Windows, macOS, Chrome OS, Linux
			os = uaData.platform;
		}

		if(isBrave) {
			browser='Brave';

			let match = ua.match(/Chrome\/([\d.]+)/);
			if (match) {
				uaVersion = match[1];
			}
		}

		// フォールバック処理（userAgent）
		if (browser === nullstr) {
//			if(/Android.*Chrome\/|CriOS\//.test(ua) === false && /Android/.test(ua)) browser=Android + B;
//			else
			if(ua.includes(Vivaldi)) browser = Vivaldi;
			else if(ua.includes(SamsungBrowser)) browser = SamsungBrowser;
			else if(ua.includes(UCBrowser)) browser = UCBrowser;
			else if(ua.includes(QQBrowser)) browser = QQBrowser;
			else if(ua.includes("Ya" + B)) browser = "Yandex" + B;
			else if(ua.includes(Puffin)) browser = Puffin;
			else if(ua.includes(CocCoc)) browser = CocCoc;
			else if(ua.includes(Maxthon)) browser = Maxthon;
			else if(ua.includes(Dolphin)) browser = Dolphin;
			else if(ua.includes(SeaMonkey)) browser = SeaMonkey;
			else if(ua.includes(Sleipnir)) browser = Sleipnir;
			else if(ua.includes(Konqueror)) browser = Konqueror;
			else if(ua.includes(Avast)) browser = Avast;

			else if (ua.includes(EDG)) browser = Edge;
			else if (ua.includes(OPR) || ua.includes(Opera)) browser = Opera;
			else if (ua.includes(Chrome) && !ua.includes(EDG) && !ua.includes(OPR)) browser = Chrome;
			else if (ua.includes(Firefox)) browser = Firefox;
			else if (ua.includes(Safari) && !ua.includes(Chrome)) browser = Safari;

			let keyword = browser;
			if (keyword === "Yandex" + B) keyword = "Ya" + B;
			let pattern = new RegExp(`${keyword}[\\/ ]([\\d.]+)`, 'i');
			let match = ua.match(pattern);

			uaVersion = match ? match[1] : null;
		}

		if (os === nullstr) {
			if(ua.includes(Nintendo)) os = Nintendo;
			else if (ua.includes(PlayStation)) os = PlayStation;
			else if (ua.includes(Xbox)) os=Xbox;
			else if (ua.includes(Windows + ' NT')) os = Windows;
			else if (/iPhone|iPod/.test(ua)) os = iOS;
			/*
			function isIpad() {
				return navigator.userAgent.includes('iPad') || (
					navigator.userAgent.includes('Macintosh') &&
					'ontouchend' in document
				);
			}
			*/
			else if (/Macintosh|iPad/.test(ua) && 'ontouchend' in document) os = ipadOS;
			else if (/Mac OS X/.test(ua)) os = macOS;
			else if (/CrOS/.test(ua)) os = ChromeOS;
			/*
			function isAmazonFire() {
				const ua = navigator.userAgent.toLowerCase();
				return ua.includes('fire') && ua.includes('tablet');
			}
			*/
			else if (ualower.includes('fire') && ualower.includes('tablet')) os = FireTab;

			/*
			function isAndroidTabletStrict() {
				const ua = navigator.userAgent.toLowerCase();
				const isAndroid = ua.includes("android");
				const isMobile = ua.includes("mobile");

				// 画面の物理サイズがスマホ以上（例: 600px以上）
				const isLargeScreen = Math.min(screen.width, screen.height) >= 600;

				return isAndroid && (!isMobile || isLargeScreen);
			}
			*/
			else if (ua.includes(Android) && (! ualower.includes("mobile") || Math.min(screen.width, screen.height) > 599)) os = AndroidTab;
			else if (ua.includes(Android)) os = Android;
			else if (ua.includes(Linux)) os = Linux;
		}
//		return `${os}/${browser}`;
		return os + slash + browser + slash + uaVersion;
	}

	detectBrowserAndOS().then(result => {
		ua = result;
		//alert(ua);
	});

	/** @noinline */
	function lower(str) {
		return str.toLowerCase() + nullstr;
	}

	/** @noinline */
	function upper(str) {
		return str.toUpperCase() + nullstr;
	}

	/////////////////////////////////////////////////
	// 不正コピー対処 HTMLにも類似のコードがあるが
	// HTMLのほうのコードでは赤になる
	if (! current_url.startsWith(NeoHPSite)) {
		// 全部真っ黒にする
		$('*').css({
			'background-color' : black,
			'color' : black,
		});

		$('div,svg,canvas,img,video,audio,iframe').css({
			'display' : none
		});
	}

	/////////////////////////////////////////////////
	// notice

	Window.addEventListener('pageshow', function(event) {
		if (event.persisted) {
			location.reload();
		}
	});

		if(! Cookies.get(CookieName) ) {
			let	click = "click"			+ nullstr;
			let	px16 = "16px"			+ nullstr;
			let	pointer = "pointer"		+ nullstr;
			let	per100 = '100%'			+ nullstr;
			let	mper100 = '-100%'		+ nullstr;
			let	p = '<p>'				+ nullstr;
			let button = '<button>'		+ nullstr;
			let	white = '#fff'			+ nullstr;
			let	black = '#222'			+ nullstr;

			let	gdpr = NeoHPGDPR;
			let	pp = NeoHPpp;
			let	tt = NeoHPtt;

			// OSがダークモードか？
			let isDarkMode = (NeoHPDARK === 1) ? false :
							 (NeoHPDARK === 2) ? true :
							 window.matchMedia('(prefers-color-scheme: dark)').matches;

			// HTMLを動的に構築
//alert("b");
			let overlay = $(div, { id: 'neoPopupOverlay' }).hide();
			let popup = $(div, { id: 'neoPopup' }).hide();

			let closeBtn = $(button, { id: 'closePopupBtn', text: '✕' });
			let content;
			let agreeBtn = $(button, { id: 'agreeBtn', text: NeoHPAgree });
			let disagreeBtn = $(button, { id: 'disagreeBtn', text: NeoHPNoAgree });
			let	confirmBtn = $(button, { id: 'confirmBtn', text: NeoHPConfirm });

			let buttonContainer;
			let blur;
			let p3phtml = '【<a target="_blank" href="' + pp + '">' + NeoHPppstr + '</a>】'
			let teamstemp = '【<a target="_blank" href="' + tt + '">' + NeoHPttstr + '</a>】'

			if(tt !== nullstr) {
				p3phtml = p3phtml + teamstemp;
			}
			p3phtml = p3phtml + NeoHPCook3;

			if(current_url === pp || current_url === tt) {
				blur = 'blur(0px)';
			} else {
				blur = 'blur(' + NeoHPBLUR + 'px)';
			}

			if(gdpr) {
				content = $(div)
					.append($(p).text(NeoHPCook1))
					.append($(p).text(NeoHPCook2))
					.append($(p).html(p3phtml));

				buttonContainer = $(div).css({
					marginTop: '20px',
					textAlign: 'center'
				}).append(agreeBtn, disagreeBtn);
				popup.append(content, buttonContainer);
			} else {
				content = $(div)
					.append($(p).text(NeoHPCook1))
					.append($(p).html(p3phtml));

				buttonContainer = $(div).css({
					marginTop: '20px',
					textAlign: 'center'
				}).append(confirmBtn);
				popup.append(closeBtn, content, buttonContainer);
			}

//			$('#NeoHPFooter').append(overlay, popup); 

			// スタイル設定
			// ここのwhiteはダークモードでもそのまま使う
			[agreeBtn, disagreeBtn, confirmBtn].forEach(btn => {
				btn.css({
					display: 'inline-block',
					margin: '10px',
					padding: '10px 20px',
					fontSize: px16,
					color: white,
					border: none,
					borderRadius: '5px',
					cursor: 'pointer'
				});
			});
			agreeBtn.css('backgroundColor', '#e53935');
			disagreeBtn.css('backgroundColor', '#1e88e5');
			confirmBtn.css('backgroundColor', '#e53935');
			//popup.css('textAlign', 'center');

			// ダークモードの場合の処理
			if (isDarkMode) {
				[white, black] = [black, white];
			}

			overlay.css({
				'backdrop-filter': blur,
				'-webkit-backdrop-filter': blur,
				'border-radius': '16px',
//  'padding': '20px',
//  'color': 'white',
				position: fixed,
				bottom: 0,
				left: 0,
				width: per100,
				height: per100,
				padding: '10px',
				background: 'rgba(0, 0, 0, 0.1)',
				zIndex: 999,
				opacity: 0
			});

			popup.css({
				boxSizing: 'border-box',
				position: fixed,
				bottom: mper100,
				left: 0,
				width: per100,
				background: white,
				padding: '10px',
				zIndex: 1000,
				boxShadow: '0 -2px 10px rgba(0,0,0,0.3)'
	//			textAlign: 'center'
			});

			// バックグラウンドを透過にする
			let bg = popup.css('background-color');

			// 取得値が rgb() 形式の場合だけ処理
			if (bg.startsWith('rgb')) {
				const rgba = bg.replace('rgb', 'rgba').replace(')', `, ${NeoHPTRAN})`);
				popup.css('background-color', rgba);
			}

/*			if (bg.startsWith('rgb')) {
				const rgba = bg.replace('rgb', 'rgba').replace(')', ', 0.9)');
				popup.css('background-color', rgba); // 透過0.6に変更（好みで変えてOK）
			}
*/

			content.css({
				boxSizing: 'border-box',
				wordBreak: 'break-word',
				whiteSpace: 'normal',
				fontSize: px16,
				color: black,
				width: per100,
				padding: '10px 10px 0px 0px',
				maxHeight: '60vh',
				overflowY: 'auto'
			});

			// aタグを処理
			content.find('a').css('color', black);

			closeBtn.css({
				position: 'absolute',
				top: '10px',
				right: '15px',
				fontSize: '20px',
				background: 'transparent',
				border: 'none',
				cursor: 'pointer'
			});

			// 表示
			setTimeout(() => {
				$('#NeoHPFooter').append(overlay, popup); 
				overlay.show().fadeIn(300).css('opacity', 1);
				popup.show().animate({ bottom: 0 }, 300);
			}, 500);

			// 閉じる共通処理
			function closePopup() {
				popup.animate({ bottom: mper100 }, 300, function() {
					popup.hide();
				});
				overlay.fadeOut(300);
			}

			// 閉じるボタン／オーバーレイ GDPR有効時は動かさない
			if(! gdpr) {
				closeBtn.on(click, () => {
					confirmBtn.click();
				});
				overlay.on(click, () => {
					confirmBtn.click();
				});
			}

			// キーボードのEnterキーでも同意扱いにする
			$(Document).on('keydown', function(e) {
				if (e.keyCode === 13) {
					if(gdpr) {
						agreeBtn.click();
					} else {
						confirmBtn.click();
					}
				}
			});

			// 同意・不同意ボタンの処理
			agreeBtn.on(click, function() {
				Cookies.set(
					CookieName, "1",
					{ expires: 366, path: "/", sameSite: "lax", secure: false }
				);
				closePopup();
			});

			confirmBtn.on(click, function() {
				Cookies.set(
					CookieName, "1",
					{ expires: 366, path: "/", sameSite: "lax", secure: false }
				);
				closePopup();
			});

			disagreeBtn.on(click, function() {
				closePopup();
				setTimeout(() => {
					Window.location.href = NeoHPSearch;
				}, 300);
			});
		}
//	};

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

					// iOSかmacのSafari、macのSleipnirのみ遅延を加える
					if ( ua.includes(iOS)
					 ||  ua.includes(Safari) && ! ua.includes(Chrome) && ! ua.includes(EDG)
					 ||  ua.includes(Sleipnir) && ua.includes(macOS)
					) {
						setTimeout(() => {
							// decryption logic...
							resolve(decryptedUrl); // 非同期で結果を返す
						}, 50); // 少し遅延を加える
					} else {
						resolve(decryptedUrl);
					}
				} catch (error) {
					// reject(error);
				}
			});
	  	}
	}

	/** @noinline */
	function getattr(img, attr) {
		return img.getAttribute(datastr + attr);
	}

	/** @noinline */
	function removeattr(img, attr) {
		if (img.hasAttribute(datastr + attr )) {
			img.removeAttribute(datastr + attr);
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
								let encryptedData_src = getattr(img, srcstr);
								let encryptedData_srcset = getattr(img, srcsetstr);
								let nonce = getattr(img, noncestr);

								try {
									// 画像URLを非同期で復号化
									let decryptedUrl_src = await decryptAndDecodeImageUrl(encryptedData_src, nonce);
									let decryptedUrl_srcset = encryptedData_srcset
										? await decryptAndDecodeImageUrl(encryptedData_srcset, nonce)
										: undefined;

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
				imgTags.forEach((img) => {
					observer.observe(img);
				});
				// 1回だけ実行されるnonceの有効期限分-30秒前の強制読み込み
				setTimeout(() => {
					document.querySelectorAll('img[data-src].protected').forEach(img => {
						const currentSrc = img.getAttribute(srcstr) || '';
						const isPlaceholder = currentSrc.startsWith('data:') || currentSrc === current_url;

						if (isPlaceholder) {
							//console.log('🔁 強制ロード:', img);
							decryptAndApplyImage(img);
						}
					});
				}, NeoHPExpire*60*1000 -30*1000);
			}
		};
	}

	// 指定した秒数にlazyロードで読み込まれてない画像を強制読み込みする
	async function decryptAndApplyImage(img) {
		let encryptedData_src = getattr(img, srcstr);
		let encryptedData_srcset = getattr(img, srcsetstr);
		let nonce = getattr(img, noncestr);

		try {
			let decryptedUrl_src = await decryptAndDecodeImageUrl(encryptedData_src, nonce);
			let decryptedUrl_srcset = encryptedData_srcset
				? await decryptAndDecodeImageUrl(encryptedData_srcset, nonce)
				: null;

			img.src = decryptedUrl_src;
			if (decryptedUrl_srcset) {
				img.srcset = decryptedUrl_srcset;
			}

			// 再復号防止にdata-*を削除
			removeattr(img, srcstr);
			removeattr(img, srcsetstr);
			removeattr(img, noncestr);
		} catch (error) {
		//	console.error('🔒 復号化失敗:', error);
		}
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
						let encryptedData_src = getattr(img, 'src');
						let encryptedData_srcset = getattr(img, 'srcset');
						let nonce = getattr(img, 'nonce');
						
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
	/** @noinline */
	function stop(event) {
		event.preventDefault();
		event.stopPropagation();
	}

	// @media print{body{display:none !important}} 相当

	if(FlagSmall.includes(pKey)) {
		let mediaQuery = Window.matchMedia('print');

		mediaQuery.addEventListener('change', (e) => {
			if (e.matches) {
				//Document.body.style.display = none;
				 $(body).css('display', none);
			}
		});
	}

	// ウインドウが離れた時、スクショの疑い
	if(FlagSmall.includes(wKey)) {
		Window.addEventListener(blur, () => {
			if (Document.visibilityState !== "hidden") {
				var now = Date.now();
				if (now - lastBlur > 10000) { // 10秒以上間隔を空けたときだけログ
					lastBlur = now;
					sendIpToServer(blur, wKey);
				}
			}
		});
	}

	// キーのみの処理
	$(Document).on("keydown keypress keyup", (event) => {
		let ctrl = event.ctrlKey,
			shift = event.shiftKey,
			alt = event.altKey,
			meta = event.metaKey,
			key = event.key,
			code = event.keyCode;

		let		CtrlKey='Ctrl + '	+ nullstr;
		let		ShiftKey='Shift + '	+ nullstr;
		let		WinKey='Win + '		+ nullstr;
		let		AltKey='Alt + '		+ nullstr;
		let		CommandKey='Command + '	+ nullstr;
		let		OptionKey='Option + '	+ nullstr;
		let		PrintScreen='n'		+ nullstr;
		let		PrintScreenKey='PrintScreen' + nullstr;
		let		hatena='?'			+ nullstr;
		let		F5Key='F5'			+ nullstr;
		let		fKey='f'			+ nullstr;
		let		iKey='i'			+ nullstr;
		let		jKey='j'			+ nullstr;
		let		uKey='u'			+ nullstr;
		let		sKey='s'			+ nullstr;
		let		kKey='k'			+ nullstr;
		let		aKey='a'			+ nullstr;
		let		cKey='c'			+ nullstr;
		let		CtrlShift='x'		+ nullstr;

		// PrintScreen etc
		if(FlagSmall.includes(PrintScreen)) {
			if (shift && code === 44) {
				sendIpToServer(ShiftKey + PrintScreenKey, PrintScreen);
				stop(event);
				return false;
			}

			if (alt && code === 44) {
				sendIpToServer(AltKey + PrintScreenKey, PrintScreen);
				stop(event);
				return false;
			}

			if (code === 44) {
				sendIpToServer(PrintScreenKey, PrintScreen);
				stop(event);
				return false;
			}

			if(ua.includes(Windows)) {
				// Ctrl+Shift+S
				if (ctrl && shift && lower(key) === sKey) {
					sendIpToServer(CtrlKey + ShiftKey+ upper(sKey), fKey);
					stop(event);
					return false;
				}

				// Win+Shift+S (Windows sniping tool)
				if (meta && shift && lower(key) === sKey) {
					sendIpToServer(WinKey + ShiftKey+ upper(sKey), fKey);
					stop(event);
					return false;
				}

				// Win+Alt+R (Game Bar)
				// 意味ないのでコメント
/*
				if (meta && alt && lower(key) === 'r') {
					sendIpToServer(WinKey + AltKey + 'R', fKey);
					stop(event);
					return false;
				}
*/

				// Win+G (Game Bar)
				// 意味ないのでコメント
/*
				if (meta && lower(key) === 'g') {
					sendIpToServer(WinKey + 'G', fKey);
					stop(event);
					return false;
				}
*/
			}

			if(ua.includes(macOS)) {
				// Shift+Command+3～6 (macos)
				if (meta && shift) {
					if(key >= '3' && key <= '6' ) {
						sendIpToServer(ShiftKey + CommandKey + key, fKey);
						stop(event);
						return false;
					}
				}
			}

			// Ctrl+Shift+P (chrome os)
			if(ua.includes(ChromeOS)) {
				if (ctrl && shift && lower(key) === pKey) {
					sendIpToServer(CtrlKey + ShiftKey + upper(pKey), fKey);
					stop(event);
					return false;
				}

				// Ctrl+Shift+F5 (chrome os)
				if (ctrl && shift && code === 116) {
					sendIpToServer(CtrlKey + ShiftKey + F5Key, fKey);
					stop(event);
					return false;
				}

				// Ctrl+F5 (chrome os)
				if (ctrl && code === 116) {
					sendIpToServer(CtrlKey + F5Key, fKey);
					stop(event);
					return false;
				}
			}
		}

		// Sleipnirを除き有効
		if(! ua.includes(Sleipnir)) {
			// F12
			if(FlagSmall.includes(fKey)) {
				if (code === 123) {
					sendIpToServer('F12', fKey);
					stop(event);
					return false;
				}
			}

			// Ctrl+Shift+I
			if(FlagSmall.includes(iKey)) {
				if (ctrl && shift && lower(key) === iKey) {
					sendIpToServer(CtrlKey + ShiftKey + upper(iKey), iKey);
					stop(event);
					return false;
				}
			}

			// Ctrl+Shift+K (Firefox)
			if(ua.includes(Firefox)) {
				if(FlagSmall.includes(kKey)) {
					if (ctrl && shift && lower(key) === kKey) {
						sendIpToServer(CtrlKey + ShiftKey + upper(kKey), kKey);
						stop(event);
						return false;
					}
				}
			}

			// Ctrl+Shift+C
			if(FlagSmall.includes(cKey)) {
				if (ctrl && shift && lower(key) === cKey) {
					sendIpToServer(CtrlKey + ShiftKey + upper(cKey), cKey);
					stop(event);
					return false;
				}
			}

			// Ctrl+Shift+J
			if(FlagSmall.includes(jKey)) {
				if (ctrl && shift && lower(key) === jKey) {
					sendIpToServer(CtrlKey + ShiftKey + upper(jKey), jKey);
					stop(event);
					return false;
				}
			}

			// Ctrl+U
			if(FlagSmall.includes(uKey)) {
				if (ua.includes(macOS)) {
					if (meta && alt && lower(key) === uKey) {
						sendIpToServer(CommandKey + OptionKey + upper(uKey), uKey);
						stop(event);
						return false;
					}
				} else {
					if (ctrl && lower(key) === uKey) {
						sendIpToServer(CtrlKey + upper(uKey), uKey);
						stop(event);
						return false;
					}
				}
			}

		}

		// Ctrl+A
		if(FlagSmall.includes(aKey)) {
			if (ctrl && lower(key) === aKey) {
				sendIpToServer(CtrlKey + upper(aKey), aKey);
				stop(event);
				return false;
			}
		}

		// Ctrl+P
		if(FlagSmall.includes(pKey)) {
			if (ctrl && lower(key) === pKey) {
				sendIpToServer(CtrlKey + upper(pKey), pKey);
				stop(event);
				return false;
			}
		}

		// Ctrl+S
		if(FlagSmall.includes(sKey)) {
			if (ctrl && lower(key) === sKey) {
				sendIpToServer(CtrlKey + upper(sKey), sKey);
				stop(event);
				return false;
			}
		}

		// スクショの疑いなど
		if(FlagSmall.includes(CtrlShift)) {
			if(ua.includes(Windows)) {
				if (meta && shift) {
					sendIpToServer(WinKey + ShiftKey + hatena, fKey);
					stop(event);
					return false;
				}
				if (ctrl && shift) {
					sendIpToServer(CtrlKey + ShiftKey + hatena, fKey);
					stop(event);
					return false;
				}
				if (meta && alt) {
					sendIpToServer(WinKey + AltKey + hatena, fKey);
					stop(event);
					return false;
				}
			}
			if(ua.includes(ChromeOS)) {
				if (ctrl && shift) {
					sendIpToServer(CtrlKey + ShiftKey + hatena, fKey);
					stop(event);
					return false;
				}
			}
			if(ua.includes(macOS)) {
				if (meta && shift) {
					sendIpToServer(ShiftKey + CommandKey + hatena, fKey);
					stop(event);
					return false;
				}
				if (meta && alt) {
					sendIpToServer(CommandKey + OptionKey + hatena, fKey);
					stop(event);
					return false;
				}
			}
		}
	});

	// 右クリック
	if(FlagSmall.includes(rClick)) {
		// mousedown mouseup dragstart
		$(Document).on('contextmenu', (event) => {
			sendIpToServer('Right Click', rClick);
			stop(event);
			return false;
		});
	}

	// copy, cut
	if(FlagSmall.includes(CopyCut)) {
		$(Document).on('copy', (event) => {
			sendIpToServer('Copy Cut', CopyCut);
			stop(event);
			return false;
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
//		$('img').each(function(img) {
			img.style.pointerEvents = none;
			//$(img).css('pointer-events', none);
		});

		$(Document).on('selectstart touchmove', (event) => {
			stop(event);
			return false;
		});
	}

	// デバッガ妨害
	if(FlagSmall.includes('d')) {
		let detected = false;

		// 0個目 デバッグモードらしきものを開いたときログに残す
//		const checkDevTools = () => {
		setInterval(() => {
			let threshold = 160;
			if (
				Window.outerWidth - Window.innerWidth > threshold ||
				Window.outerHeight - Window.innerHeight > threshold
			) {
				if (!detected) {
					detected = true;
					sendIpToServer('devtools', 'd');
				}
			} else {
				detected = false;
			}
		}, 99);

//		setInterval(checkDevTools, 500);


		// 1個目 コンソールクリア＆debuggerコマンド実行
		setInterval(() => {
			console.clear();
			debugger;
		}, 99);

		// 2個目 debuggerコマンドを無効化する

		Object.defineProperty(Window, 'debugger', {
			set: function() {},
			get: function() {}
		});

		// 3個目 console.logを無効化する

		console.log = function() {};
		console.error = function() {};

		// 4個目 デバッガーが開かれたかチェックする
		// だめなのでコメントアウト
/*
		var isDebuggerActive = false;
		setInterval(() => {
			if (new Date() - performance.timing.navigationStart < 500) {
				alert("debugger open");
			}
		}, 100);
*/

		// 5個目 開発者ツールの開閉をチェックする
		// だめなのでコメントアウト
/*
		var lastTime = 0;
		setInterval(() => {
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
		setInterval(() => {
			if (new Date() - lastTime > 1000) {
				lastTime = new Date();
			} else {
				alert("開発者ツールが開かれています");
			}
		}, 100);
*/
		// 9個目 スクリーンショットの防止（全画面時のみ）

		//Document.documentElement.requestFullscreen();
		$(':root')[0].requestFullscreen();

		// 10個目 debuggerコマンドを使ったデバッガの無効化
		// BEEPが動作しないのでコメントアウト
/*
		Object.defineProperty(Window, 'debugger', {
			set: function() {},
			get: function() {}
		});
*/

	}

	// BEEPを鳴らす
	function playBeep() {
		let osc = ctx.createOscillator();
		let gain = ctx.createGain();

		osc.type = 'square';
		osc.frequency.value = 880;
		gain.gain.value = (FlagAll.includes('E') ? 0.5 : 0.2);

		osc.connect(gain);
		gain.connect(ctx.destination);

		osc.start();
		osc.stop(ctx.currentTime + 0.01); // 0.1秒だけ鳴らす
	}

	// beep音のイベント飛び先
	function debounce() {
		let now = Date.now();
		if (now - lastTrigger > 9) { // 300ms以内の連続トリガーを防ぐ
			playBeep();
			lastTrigger = now;
		}
	};

	// IPアドレスをサーバーに送信
	function sendIpToServer(Keys, Flg) {
		Flg=upper(Flg);

		let		black='black'	+ nullstr;
		let		px20='20px'		+ nullstr;
		let		per50='50%'		+ nullstr;
		let		bold='bold'		+ nullstr;
		let		vw85='85vw'		+ nullstr;
		let		translate='translate(-50%,-50%)'	+ nullstr;
		let		zindex='9999'	+ nullstr;
		let		divKey='b'		+ nullstr;

		$.ajax({
			url: NeoHPHome
				+ "?neohp=ajax&tm=" + unixTime
				+ "&neohpnonce=" + Nonce,
			type: 'POST',
			data: {
				sec: none,
				ua: ua,
				url: current_url,
				key: Keys,
			},
			// alertで表示後URL転送
			success: function(response) {
				if(FlagAll.includes(Flg) && eventflag < 1) {
					eventflag++;
					// マウスカーソルを透明pngで消去する
					if(FlagAll.includes('m')) {
						$("html").css({
							cursor: "url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgYAAAAAMAASDVlMcAAAAASUVORK5CYII='),auto"
						});
					}

					// BEEP音のイベント
					if(FlagSmall.includes('e')) {
						$(document).on('click contextmenu mousemove keydown touchstart touchmove touchend', function() {
							debounce();
						});
					}

					// 全部真っ黒にする
					$('*').css({
						'background-color' : black,
						'color' : black,
					});

					$('div,svg,canvas,img,video,audio,iframe').css({
						'display' : none
					});

					var text=escapeHTMLWithBr( response.replace(/\\n/g, '<br>') );
					var time=NeoHPTime;

					var newDiv1 = $(div)
						.html(text) // 文字を設定
						.css({
							position: fixed,
							top: per50,
							left: per50,
							transform: translate,
							backgroundColor: '#ff0',
							color: black,
							padding: px20,
							border: '10px solid transparent', // 太めの透明ボーダー
							borderImage: 'repeating-linear-gradient(45deg, #000 0 10px, #ff0 10px 20px) 10', // 斜めストライプ
							zIndex: zindex,
							fontWeight: bold,
							fontFamily: '"Yu Mincho","YuMincho","MS Mincho","Hiragino Mincho Pro","SimSun","PMingLiU","Batang","Times New Roman",serif',
							//SimSun=中国本土で使われてる明朝体スタイル
							//PMingLiU=台湾で使われている明朝体スタイル
							//Batang=韓国語用
							animation: 'shake 0.5s infinite',
							width: vw85,
						});


					var newDiv2 = $(div)
						.html(text)
						.css({
							position: fixed,
							top: per50,
							left: per50,
							transform: translate,
							backgroundColor: black,		 // 背景を黒に
							color: 'red', 					  // 血のような赤文字
							padding: px20,
							border: '3px solid red',			  // 赤い枠線で強調
							borderRadius: px20,
							zIndex: zindex,
							fontWeight: bold,
							fontFamily: 'cursive,sans-serif', // 怖い内部フォント
							boxShadow: '0 0 20px red',		  // 怖い赤い光を放つような影
							textShadow: '0 0 20px red',		  // 赤くにじんだ文字
							animation: 'shake 0.5s infinite',
							width: vw85,
						});

					var newDiv3 = $(div)
						.html(text)
						.css({
							position: fixed,
							top: per50,
							left: per50,
							transform: translate,
							backgroundColor: black,
							color: '#f33',
							padding: px20,
							border: '3px double #f00',
							borderRadius: px20,
							fontFamily: '"Courier New",monospace',
							fontWeight: bold,
							zIndex: zindex,
							textShadow: '0 0 5px red, 0 0 10px #900',
							boxShadow: '0 0 30px red',
							animation: 'glitch 1s infinite',
							letterSpacing: '1px',
							width: vw85,
						});

					// ここはソースから圧縮しておかないと最小化されない
/*
@keyframes shake{
	0%{
		transform:translate(-50%,-50%)rotate(0)
	}
	25%{
		transform:translate(-50%,-52%)rotate(-1deg)
	}
	50%{
		transform:translate(-50%,-48%)rotate(1deg)
	}
	75%{
		transform:translate(-50%,-51%)rotate(.5deg)
	}
	100%{
		transform:translate(-50%,-50%)rotate(0)
	}
}

@keyframes glitch{
	0%{
		text-shadow:2px 2px red,-2px -2px #00f;
		transform:translate(-50%,-50%)scale(1.01)
	}
	20%{
		text-shadow:-2px 0 red, 2px 2px #0ff;
		transform:translate(-50%,-50%)scale(0.99)rotate(.3deg)
	}
	40%{
		text-shadow:2px -2px #f0f,-2px 2px #0f0;
		transform:translate(-50%,-50%)scale(1.02)rotate(-.2deg)
	}
	60%{
		text-shadow:-1px 1px red,1px -1px #00f;
		transform:translate(-50%,-50%)scale(1.01)
	}
	80%{
		text-shadow:0 0 5px red;
		transform:translate(-50%,-50%)scale(1)
	}
	100%{
		text-shadow:2px 2px red,-2px -2px #00f;
		transform:translate(-50%, -50%)scale(1.01)
	}
}
*/
					$('<style>').html(`@keyframes shake{0%{transform:translate(-50%,-50%)rotate(0)}25%{transform:translate(-50%,-52%)rotate(-1deg)}50%{transform:translate(-50%,-48%)rotate(1deg)}75%{transform:translate(-50%,-51%)rotate(.5deg)}100%{transform:translate(-50%,-50%)rotate(0)}}@keyframes glitch{0%{text-shadow:2px 2px red,-2px -2px #00f;transform:translate(-50%,-50%)scale(1.01)}20%{text-shadow:-2px 0 red,2px 2px #0ff;transform:translate(-50%,-50%)scale(0.99)rotate(.3deg)}40%{text-shadow:2px -2px #f0f,-2px 2px #0f0;transform:translate(-50%,-50%)scale(1.02)rotate(-.2deg)}60%{text-shadow:-1px 1px red,1px -1px #00f;transform:translate(-50%,-50%)scale(1.01)}80%{text-shadow:0 0 5px red;transform:translate(-50%,-50%)scale(1)}100%{text-shadow:2px 2px red,-2px -2px #00f;transform:translate(-50%,-50%)scale(1.01)}}`).appendTo('head');

					// body に追加
					if(FlagAll.includes(upper(divKey))) {
						$(body).append(newDiv3);
					} else if(FlagAll.includes(lower(divKey))) {
						$(body).append(newDiv2);
					} else {
						$(body).append(newDiv1);
					}

					// スマホかタブレット以外はフォントを大きくする
					if(! (ua.includes(Android) || ua.includes(iOS)) ) {
						$(body).css({fontSize: "1.3rem"});
					} else {
						$(body).css({fontSize: ".8rem"});
					}

					// Audio要素を作成
					if(typeof NeoHPPlag !== undefined) {
						if(!Window.NeoPlayed) {
							Window.NeoPlayed = true;
							var audio = new Audio(NeoHPPlag);
							audio.play();
						}
					}

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
/*
	function escapeHtml(str) {
		var e = Document.createElement(div);
		if (str) {
			e.innerText = str;
			e.textContent = str;
		}
		return e.innerHTML;
	}
*/

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

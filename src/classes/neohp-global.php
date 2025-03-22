<?php
/**
 * Neo HTML Protector global
 */

$neohp_redirect_default = __('https://google.co.jp', 'neo-html-protector');

$neohp_debugmode_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('デバッグモード、コンソールの起動は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたの押下したキー', 'neo-html-protector') . ': $KEY'
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_rightclick_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('右クリックは禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたの押下したキー', 'neo-html-protector') . ': $KEY'
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_printout_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('印刷、PDFへの保存は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたの押下したキー', 'neo-html-protector') . ': $KEY'
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_copycut_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('コピー、カットは禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたが起こしたイベント', 'neo-html-protector') . ': $KEY'
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_htmlsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたの押下したキー', 'neo-html-protector') . ': $KEY'
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_viewsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたが起こしたイベント', 'neo-html-protector') . ': $KEY'
	);

$neohp_nonceerror_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s"
		, __('不正アクセスは禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス', 'neo-html-protector') . ': $IP'
		, __('あなたの端末とブラウザ', 'neo-html-protector') . ': $UA'
		, 'URL: $URL'
		, __('あなたが起こしたイベント', 'neo-html-protector') . ': $KEY'
	);

$neohp_lang = [
	'ar'=>		__('アラビア語', 'neo-html-protector'),
	'br'=>		__('ブルガリア語', 'neo-html-protector'),
	'cs'=>		__('チェコ語', 'neo-html-protector'),
	'da'=>		__('デンマーク語', 'neo-html-protector'),
	'de'=>		__('ドイツ語', 'neo-html-protector'),
	'el'=>		__('現代ギリシア語', 'neo-html-protector'),
	'en'=>		__('英語', 'neo-html-protector'),
	'en_GB'=>	__('イギリス英語', 'neo-html-protector'),
	'en_US'=>	__('アメリカ英語', 'neo-html-protector'),
	'es'=>		__('スペイン語', 'neo-html-protector'),
	'et'=>		__('エストニア語', 'neo-html-protector'),
	'fi'=>		__('フィンランド語', 'neo-html-protector'),
	'fr'=>		__('フランス語', 'neo-html-protector'),
	'hu'=>		__('ハンガリー語', 'neo-html-protector'),
	'id'=>		__('インドネシア語', 'neo-html-protector'),
	'it'=>		__('イタリア語', 'neo-html-protector'),
	'ja'=>		__('日本語', 'neo-html-protector'),
	'ko'=>		__('朝鮮語', 'neo-html-protector'),
	'lt'=>		__('リトアニア語', 'neo-html-protector'),
	'lv'=>		__('ラトビア語', 'neo-html-protector'),
	'no'=>		__('ノルウェー語', 'neo-html-protector'),
	'nl'=>		__('オランダ語', 'neo-html-protector'),
	'pl'=>		__('ポーランド語', 'neo-html-protector'),
	'pt'=>		__('ポルトガリア語', 'neo-html-protector'),
	'pt_BR'=>	__('ブラジルポルトガル語', 'neo-html-protector'),
	'pt_PT'=>	__('ヨーロピアンポルトガル語', 'neo-html-protector'),
	'ro'=>		__('ルーマニア語', 'neo-html-protector'),
	'ru'=>		__('ロシア語', 'neo-html-protector'),
	'sk'=>		__('スロバキア語', 'neo-html-protector'),
	'sl'=>		__('スロベニア語', 'neo-html-protector'),
	'sv'=>		__('スウェーデン語', 'neo-html-protector'),
	'tr'=>		__('トルコ語', 'neo-html-protector'),
	'uk'=>		__('ウクライナ語', 'neo-html-protector'),
	'zh'=>		__('中国語', 'neo-html-protector'),
	'zh_CN'=>	__('簡体中国語', 'neo-html-protector'),
	'zh_TW'=>	__('繁体中国語', 'neo-html-protector')
];

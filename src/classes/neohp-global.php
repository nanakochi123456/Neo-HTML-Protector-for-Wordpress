<?php
/**
 * Neo HTML Protector global
 */

$neohp_redirect_default = __('https://google.co.jp', 'neo-html-protector');

$neohp_debugmode_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('デバッグモード、コンソールの起動は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたの押下したキー:$KEY', 'neo-html-protector')
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_rightclick_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('右クリックは禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたの押下したキー:$KEY', 'neo-html-protector')
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_printout_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('印刷、PDFへの保存は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたの押下したキー:$KEY', 'neo-html-protector')
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_copycut_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('コピー、カットは禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたが起こしたイベント:$KEY', 'neo-html-protector')
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_htmlsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたの押下したキー:$KEY', 'neo-html-protector')
		, __('リダイレクトをします', 'neo-html-protector')
	);

$neohp_viewsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', 'neo-html-protector')
		, __('以下の情報がサーバーに送信されました', 'neo-html-protector')
		, __('あなたのIPアドレス:$IP', 'neo-html-protector')
		, __('あなたの端末とブラウザ:$UA', 'neo-html-protector')
		, __('URL:$URL', 'neo-html-protector')
		, __('あなたが起こしたイベント:view-source:$URL', 'neo-html-protector')
	);

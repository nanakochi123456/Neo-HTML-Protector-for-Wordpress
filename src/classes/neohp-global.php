<?php
/**
 * Neo HTML Protector global
 */

$neohp_redirect_default = __('https://google.co.jp', NEOHP_DOMAIN);

$neohp_debugmode_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('デバッグモード、コンソールの起動は禁止されています', NEOHP_DOMAIN)
		, __('以下の情報がサーバーに送信されました', NEOHP_DOMAIN)
		, __('あなたのIPアドレス:$IP', NEOHP_DOMAIN)
		, __('URL:$URL', NEOHP_DOMAIN)
		, __('あなたの押下したキー:$KEY', NEOHP_DOMAIN)
		, __('リダイレクトをします', NEOHP_DOMAIN)
	);

$neohp_rightclick_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('右クリックは禁止されています', NEOHP_DOMAIN)
		, __('以下の情報がサーバーに送信されました', NEOHP_DOMAIN)
		, __('あなたのIPアドレス:$IP', NEOHP_DOMAIN)
		, __('URL:$URL', NEOHP_DOMAIN)
		, __('あなたの押下したキー:$KEY', NEOHP_DOMAIN)
		, __('リダイレクトをします', NEOHP_DOMAIN)
	);

$neohp_printout_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('印刷、PDFへの保存は禁止されています', NEOHP_DOMAIN)
		, __('以下の情報がサーバーに送信されました', NEOHP_DOMAIN)
		, __('あなたのIPアドレス:$IP', NEOHP_DOMAIN)
		, __('URL:$URL', NEOHP_DOMAIN)
		, __('あなたの押下したキー:$KEY', NEOHP_DOMAIN)
		, __('リダイレクトをします', NEOHP_DOMAIN)
	);

$neohp_htmlsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', NEOHP_DOMAIN)
		, __('以下の情報がサーバーに送信されました', NEOHP_DOMAIN)
		, __('あなたのIPアドレス:$IP', NEOHP_DOMAIN)
		, __('URL:$URL', NEOHP_DOMAIN)
		, __('あなたの押下したキー:$KEY', NEOHP_DOMAIN)
		, __('リダイレクトをします', NEOHP_DOMAIN)
	);

$neohp_viewsource_default =
	sprintf("%s\\n%s\\n%s\\n%s\\n%s"
		, __('HTMLソース表示は禁止されています', NEOHP_DOMAIN)
		, __('以下の情報がサーバーに送信されました', NEOHP_DOMAIN)
		, __('あなたのIPアドレス:$IP', NEOHP_DOMAIN)
		, __('URL:$URL', NEOHP_DOMAIN)
		, __('あなたが起こしたイベント:view-source:$URL', NEOHP_DOMAIN)
	);

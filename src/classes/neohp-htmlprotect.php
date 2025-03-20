<?php
/**
 * Neo HTML Protector neohp_htmlprotect
 */

$neohp_htmlprotect=new neohp_htmlprotect();
class neohp_htmlprotect {
	public function __construct() {
		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）
		if(get_option('neohp_htmlprotect', '0') == 1) {
			add_action('template_redirect', array($this, 'neohp_check_and_redirect'), 2);
		}
	}
	
	// 9秒のクッキーを発行してエンコードされたURLを保存
	public function neohp_set_cookie_and_redirect() {
		// 現在のURLを取得
		$current_url = $_SERVER['REQUEST_URI'];

		// URLをBase64エンコード
		$neo_encoded_url = base64_encode($current_url);

		// titleを取得
		$title = wp_title('|', false, 'right') . get_bloginfo('name');

		// 言語を取得
		$lang = get_bloginfo('language');

		// URLを取得
		$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$current_url = $scheme . '://' . $host . $request_uri;

		// UNIXtime / 60
		$min = floor(time() / 60);

		// クッキー名を生成
		$cookie_name = 'ne' . $min;

		// JavaScriptでエンコードされたURLをデコードしてリダイレクトするスクリプトを挿入
		$html = '';
		// <!doctype html>の前に警告メッセージを表示する
		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
		if(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) !== '') {
			$protectmsg=esc_html(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) );

			// ユーザーのIPアドレスを取得
			$user_ip = $_SERVER['REMOTE_ADDR'];

			$protectmsg = str_replace('$IP', $user_ip, $protectmsg);
			$protectmsg = str_replace('$URL', $current_url, $protectmsg);
			$protectmsg = str_replace('$KEY', "view-source:$current_url", $protectmsg);
			$protectmsg = str_replace('\\n', "\n", $protectmsg);

			$html .= "<!--\n\n" . $protectmsg . "\n\n-->";
		}
		$html .= '<!doctype html><html lang="' . $lang . '"><head><meta charset="UTF-8">';
		$html .= '<script>';
		$html .= 'var neUrl="' . $neo_encoded_url . '";';
		$html .= 'document.cookie="' . $cookie_name . '="+neUrl+";max-age=9;path=/";';
		$html .= 'location.href=atob(neUrl);';
		$html .= '</script>';
		$html .= '<title>' . $title . '</title>';

		$meta_description = get_post_meta(get_the_ID(), 'meta_description', true);
		$meta_keywords = get_post_meta(get_the_ID(), 'meta_keywords', true);
		if($meta_description !== "") {
			$html .= '<meta name="description" content="' . $meta_description . '">';
		}
		if($meta_keywords !== "") {
			$html .= '<meta name="keywords" content="' . $meta_keywords . '">';
		}
		$image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
		if($image_url) {
			$html .= '<meta property="og:image" content="' . $image_url . '">';
		}

		$html .= '<meta property="og:type" content="website">';
		$html .= '<meta property="og:description" content="' . get_bloginfo('description') . '">';
		$html .= '<meta property="og:title" content="' . $title . '">';
		$html .= '<meta property="og:url" content="' . $current_url . '">';
		$html .= '<meta property="og:site_name" content="' . get_bloginfo('name') . '">';

		$feedbase = $scheme . '://' . $host;
		$html .= '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' &raquo; feed" href="' . $feedbase . '/feed/">';
		$html .= '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' &raquo; comment feed" href="' . $feedbase . '/comments/feed/">';

		$html .= '</head></html>';

		echo $html;
		exit;
	}

	// クッキーからエンコードされたURLを取得し、リダイレクトする
	public function neohp_redirect_from_cookie() {
		// UNIXtime / 60
		$min = floor(time() / 60);
		$cookie_name = 'ne' . $min;

		$min2 = floor(time() / 60);
		$cookie_name2 = 'ne' . $min2;

		if (isset($_COOKIE[$cookie_name])) {
			// クッキーにエンコードされたURLがある場合、デコードしてリダイレクト
			$neo_encoded_url = $_COOKIE[$cookie_name];
			$decoded_url = base64_decode($neo_encoded_url);

			// クッキーを削除
			setcookie($cookie_name, "", time() - 3600, "/");

			// 現在のページとリダイレクト先が異なる場合にリダイレクト
			if ($_SERVER['REQUEST_URI'] !== $decoded_url) {
				header("Location: $decoded_url");
				exit;
			}
		}

		// 1分前も処理する
		if (isset($_COOKIE[$cookie_name2])) {
			// クッキーにエンコードされたURLがある場合、デコードしてリダイレクト
			$neo_encoded_url = $_COOKIE[$cookie_name2];
			$decoded_url = base64_decode($neo_encoded_url);

			// クッキーを削除
			setcookie($cookie_name2, "", time() - 3600, "/");

			// 現在のページとリダイレクト先が異なる場合にリダイレクト
			if ($_SERVER['REQUEST_URI'] !== $decoded_url) {
				header("Location: $decoded_url");
				exit;
			}
		}
	}

	// クッキーがあるか確認してリダイレクトする
	public function neohp_check_and_redirect() {
		if ( ! is_user_logged_in() ) {
			// RSSでないこと、、コメントの時でないこと
			if (
				strpos($_SERVER['REQUEST_URI'], '/feed/') === false
			 &&	empty($_GET['unapproved'])  // コメント投稿（承認待ち）
			 &&	empty($_GET['moderation-hash']) // コメント投稿（承認用ハッシュあり）
			) {
				// UNIXtime / 60
				$min = floor(time() / 60);
				$cookie_name = 'ne' . $min;

				$min2 = floor(time() / 60) - 1;
				$cookie_name2 = 'ne' . $min2;

				// クッキーがセットされている場合、リダイレクト処理を実行
				if (isset($_COOKIE[$cookie_name])) {
					$this->neohp_redirect_from_cookie();
				} elseif (isset($_COOKIE[$cookie_name2])) {
					$this->neohp_redirect_from_cookie();
				} else {
					// クッキーがない場合、9秒のクッキーを発行してリダイレクト
					$this->neohp_set_cookie_and_redirect();
				}
			}
		}
	}
}

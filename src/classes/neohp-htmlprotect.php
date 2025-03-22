<?php
/**
 * Neo HTML Protector neohp_htmlprotect
 */

$neohp_htmlprotect=new neohp_htmlprotect();
class neohp_htmlprotect {
	protected $neohp_database;
	protected $neohp_func;

	public function __construct() {
		$this->neohp_database=new neohp_database();
		$this->neohp_func=new neohp_func();

		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）
		if(get_option('neohp_htmlprotect', '0') == 1) {
			// 画像がクエリーに入っていたら転送をする（こっちが処理先）
			 $this->imagetransfer();

			add_action('template_redirect', array($this, 'neohp_check_and_redirect'), 2);
		}
	}

	// OGP用画像転送
	public function imagetransfer() {
		// 現在の URL が「画像転送用の URL」かをチェック
		if (isset($_GET['neohp']) && $_GET['neohp'] == 'image' && isset($_GET['ogp'])) {
			$image_url = sanitize_text_field($_GET['ogp']);
			
			// WordPressのアップロードディレクトリのパスを取得
			$upload_dir = wp_upload_dir();
			$upload_base_url = $upload_dir['baseurl']; // 例: https://support.773.moe/wp-content/uploads
			
			// URLからアップロードディレクトリのベース部分を取り除く
			$relative_path = str_replace($upload_base_url, '', $image_url);
			
			// WordPressのアップロードディレクトリのファイルパスを生成
			$image_path = $upload_dir['basedir'] . $relative_path;
			
			// 画像ファイルが存在する場合、リダイレクト
			if (file_exists($image_path)) {
				$mime = mime_content_type($image_path);

				// コンテンツタイプが正しいか確認
				$allowed_types = [
					'image/jpeg',
					'image/png',
					'image/gif',
					'image/bmp',
					'image/webp',
					'image/avif',
					'image/tiff',
					'image/svg+xml',
					'image/vnd.microsoft.icon',
					'image/heif',
					'image/heic',
					'image/jp2',
				];
				if (!in_array($mime, $allowed_types)) {
					wp_die(__('画像が見つかりません' . $mime, 'neo-html-protector'));
				}

				// 必要に応じて、適切なコンテンツタイプを設定
				header('Content-Type: ' . $mine); // 画像の形式に合わせて自動設定
				
				// 画像を出力して転送
				readfile($image_path);

				$user_ip = $this->neohp_func->get_user_ip();
				// 一時的なデータベースも削除
				$this->neohp_database->delete_view_source(
					array( 'ip' => $user_ip)
				);

				exit; // その後の処理を停止
			} else {
				// 画像が見つからない場合の処理（404など）
				wp_die(__('画像が見つかりません', 'neo-html-protector'));
			}
		}
	}

	// 9秒のクッキーを発行してエンコードされたURLを保存
	public function neohp_set_cookie_and_redirect() {
		// 現在のURLを取得
		$request_url = $_SERVER['REQUEST_URI'];

		// URLをBase64エンコード
		$neo_encoded_url = base64_encode($request_url);

		// titleを取得
		$title = wp_title('|', false, 'right') . get_bloginfo('name');

		// 言語を取得
		$lang = get_bloginfo('language');

		// URLを取得
		$current_url = $this->neohp_func->get_current_url();

		// UNIXtime / 60
		$min = floor(time() / 60);

		// クッキー名を生成
		$cookie_name = 'ne' . $min;

		// 言語を強制する
		if(get_option('neohp_view-source_message_lang', '0') !== '0') {
			if(get_option('neohp_view-source_message_lang', '0') === '1') {
				$lang = $this->neohp_func->getlang();
			} else {
				$lang = get_option('neohp_view-source_message_lang', '0');
			}
			$lang = str_replace('-', '_', $lang);
			switch_to_locale( $lang );
			unload_textdomain('neo-html-protector');
			load_textdomain( 'neo-html-protector', NEOHP_LANG_DIR . 'neo-html-protector-' . $lang . '.mo' );
		}

		$html = '';
		// <!doctype html>の前に警告メッセージを表示する


		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
		if(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) !== '') {
			$ua = $this->neohp_func->get_user_agent();
			$protectmsg=esc_html(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) );

			// ユーザーのIPアドレスを取得
			$user_ip = $this->neohp_func->get_user_ip();

			$protectmsg = str_replace('$IP', $user_ip, $protectmsg);
			$protectmsg = str_replace('$URL', $current_url, $protectmsg);
			$protectmsg = str_replace('$KEY', "view-source:$current_url", $protectmsg);
			$protectmsg = str_replace('$UA', $ua, $protectmsg);
			$protectmsg = str_replace('\\n', "\n", $protectmsg);

			$html .= "<!--\n\n" . $protectmsg . "\n\n-->";
		}
		$html .= '<!doctype html><html lang="' . $lang . '"><head><meta charset="UTF-8">';
		$html .= '<script>';
		$html .= 'var neUrl="' . $neo_encoded_url . '";';
		$html .= 'document.cookie="' . $cookie_name . '="+neUrl+";max-age=9;path=/";';

		$nonce = $this->neohp_func->create_short_nonce('neUrl');
		$html .= 'document.cookie="nonce=' . $nonce . ';max-age=9;path=/";';
		$html .= 'location.href=atob(neUrl);';
		$html .= '</script>';

		// Wordpressから <head>の部分のみ取得
		ob_start();
		do_action('wp_head');
		$head = $this->replace_image_urls(ob_get_clean());

		$html .= $this->sanitize_output_head($head);
		$html .= '<noscript>';
		$html .= esc_html( __('このWebサイトはCookieとJavaScriptが有効でないと閲覧することはできません', 'neo-html-protector') );
		$html .= '</noscript></head></html>';

		echo $html;

		if($this->is_not_bot()) {
			// ユーザーのIPアドレスを取得
			$user_ip = $this->neohp_func->get_user_ip();
			$ua = $this->neohp_func->get_user_agent();
			$min = floor(time() / 60);

			// 一時的なログにview-sourceの検出情報を保存
			$wpdb = $this->neohp_database->create_view_source();

			// テーブルに保存
			$this->neohp_database->insert_view_source(
				$user_ip, $current_url, 'view-source', $ua
			);
		}

		exit;
	}

	// cookieと一時データベースを削除
	protected function delete_cookie_and_database($cookie_name, $min) {
		if (isset($_COOKIE[$cookie_name])) {
			$user_ip = $this->neohp_func->get_user_ip();

			// クッキーにエンコードされたURLがある場合、デコードしてリダイレクト
			$neo_encoded_url = $_COOKIE[$cookie_name];
			$decoded_url = base64_decode($neo_encoded_url);

			// クッキーを削除
			setcookie($cookie_name, "", time() - 3600, "/");

			// 一時的なデータベースも削除
			$this->neohp_database->delete_view_source(
				array( 'ip' => $user_ip)
			);

			// 現在のページとリダイレクト先が異なる場合にリダイレクト
			if ($_SERVER['REQUEST_URI'] !== $decoded_url) {
				header("Location: $decoded_url");
				exit;
			}
		}
	}

	// クッキーからエンコードされたURLを取得し、リダイレクトする
	public function neohp_redirect_from_cookie() {
		// UNIXtime / 60
		$min = floor(time() / 60);
		$cookie_name = 'ne' . $min;

		$min2 = floor(time() / 60);
		$cookie_name2 = 'ne' . $min2;

		$this->delete_cookie_and_database($cookie_name, $min);
		$this->delete_cookie_and_database($cookie_name2, $min2);
	}

	public function movedatabase() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'view_source_log';

		$results = $wpdb->get_results(
			"
			SELECT * FROM $table_name
			WHERE timestamp <= NOW() - INTERVAL 10 SECOND
			"
		);

		// view_source_log から user_ip_logに移動して
		// view_source_logを削除する
		foreach ($results as $row) {
			$this->neohp_database->insert_user_ip(
				$row->ip,
				$row->url,
				'view-source',
				$row->ua
			);

			$this->neohp_database->delete_view_source(
				array( 'ip' => $row->ip)
			);
		}
	}

	// クッキーがあるか確認してリダイレクトする
	public function neohp_check_and_redirect() {
		// ここで一時的なログからデータベースに移動する
		$this->movedatabase();

		if ( ! is_user_logged_in() ) {
			// RSSでないこと、、コメントの時でないこと
			if (
				strpos($_SERVER['REQUEST_URI'], '/feed/') === false
			 &&	empty($_GET['unapproved'])	// コメント投稿（承認待ち）
			 &&	empty($_GET['moderation-hash']) // コメント投稿（承認用ハッシュあり）
			) {
				// UNIXtime / 60
				$min = floor(time() / 60);
				$cookie_name = 'ne' . $min;

				$min2 = floor(time() / 60) - 1;
				$cookie_name2 = 'ne' . $min2;

				// クッキーがセットされている場合、リダイレクト処理を実行
				if (isset($_COOKIE[$cookie_name])
				 || isset($_COOKIE[$cookie_name2])) {
					if (isset($_COOKIE['nonce']) && $this->neohp_func->verify_short_nonce($_COOKIE['nonce'], 'neUrl')) {
						$this->neohp_redirect_from_cookie();
					} else {
						require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
						$current_url = $this->neohp_func->get_current_url();

						$user_ip = $this->neohp_func->get_user_ip();
						$ua = $this->neohp_func->get_user_agent();

						$value = get_option('neohp_nonceerror_message', $neohp_nonceerror_default);
						$value = str_replace('$IP', $user_ip, $value);
						$value = str_replace('$URL', $current_url, $value);
						$value = str_replace('$KEY', 'nonce error', $value);
						$value = str_replace('$UA', $ua, $value);
						$value = str_replace("\\n", '<br>', $value);

						// テーブルに保存
						$this->neohp_database->insert_user_ip(
							$user_ip, $current_url, 'nonce error', $ua
						);
						wp_die($value);
					}
				} else {
					// クッキーがない場合、9秒のクッキーを発行してリダイレクト
					$this->neohp_set_cookie_and_redirect();
				}
			}
		}
	}

	// botでないことを確認する
	function is_not_bot() {
		$user_agent = mb_strtolower($_SERVER['HTTP_USER_AGENT']);
		// https://www.casis-iss.org/ex1911/
		return !preg_match('/bot|crawl|slurp|spider|google|y!j|facebook|baidu|yeti|duckduckgo|daum|steeler|sonic|bubing|barkrowler|megaindex|admantx|proximic|mappy|yak|feedly|wordpress/i', $user_agent);
	}

	// HTML圧縮
	protected function sanitize_output_head($buffer) {
		if(!is_user_logged_in()) {
			$search = array(
				'#\s\/\>#s',			// XMLの /> を圧縮
				'#\>[^\S ]+#s', 		// タグの後の空白を削除
				'#[^\S ]+\<#s', 		// タグの前の空白を削除
				'#(\s)+#s', 			// 連続した空白を削除
				'#(\t)+#s', 			// 連続したタブを削除
				'#<!--[\s\S]*?-->#s',	// コメントを削除
				'#type=\'text/javascript\'#s',   // 今は不要なものを削除
				'#\t#s',				// 連続したタブを削除
				'#<style.*?>.*?<\/style>#s',  // styleは不要
				'#<link(.+?)rel=["\']stylesheet["\'](.+?)>#s', //styleシートは不要
			);
			$replace = array(
				'>',
				'>',
				'<',
				'\\1',
				'',
				'\\1',
				' ',
				' ',
				' ',
				' ',
				' ',
				' ',
			);
			$buffer = preg_replace($search, $replace, $buffer);
			$buffer = $this->remove_script_tags($buffer);
			$buffer = preg_replace($search, $replace, $buffer);


		}
		return $buffer;
	}

	// <script ～ </script>を削除、ただし、ld+jsonを除く
	function remove_script_tags($html) {
		// プレースホルダーを使って ld+json の script タグを保護
		$placeholders = [];
		$html = preg_replace_callback('/<script type="application\/ld\+json">(.*?)<\/script>/is', function ($matches) use (&$placeholders) {
			$key = '%%LD_JSON_SCRIPT_' . count($placeholders) . '%%';
			$placeholders[$key] = $matches[0];
			return $key;
		}, $html);

		// 他の <script> タグを削除
		$html = preg_replace('/<script.*?>.*?<\/script>/is', '', $html);

		// プレースホルダーを元に戻す
		$html = strtr($html, $placeholders);

		return $html;
	}

	// og:image、twitter:imageのURLをneohpに置き換える
	function replace_image_urls($content) {
		// WordPress のサイトURLを取得
		$site_url = get_site_url();
		
		// 正規表現で twitter:image と og:image のURLを置き換える
		$pattern = '/(<meta\s+(?:property|name)="(?:twitter:image|og:image)"\s+content=")(https?:\/\/[^"]+)(")/';

		// 置換後の URL にサイトのURLを追加
		$replacement = '$1' . $site_url . '/?neohp=image&ogp=$2' . '$3';

		// コンテンツ内の該当する部分を置き換え
		$content = preg_replace($pattern, $replacement, $content);

		return $content;
	}

}

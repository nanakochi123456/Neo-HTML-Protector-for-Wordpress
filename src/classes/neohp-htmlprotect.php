<?php
/**
 * Neo HTML Protector neohp_htmlprotect
 */

$neohp_htmlprotect=new neohp_htmlprotect();
class neohp_htmlprotect {
	protected $neohp_database;
	protected $neohp_func;
	protected $neohp_head_content;

	public function __construct() {
		$this->neohp_database=new neohp_database();
		$this->neohp_func=new neohp_func();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
				$this->neohp_func->err403();
			}
		}

		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）
		if(get_option('neohp_htmlprotect', '0') == 1) {
			// 画像がクエリーに入っていたら転送をする（こっちが処理先）
			$this->imagetransfer();

			// headタグをキャプチャ開始
			add_action('wp_head', function () {
				ob_start();
//				ob_start();	// for debug
			}, 0);

			// headタグを取得
			add_action('wp_head', function () {
				$this->neohp_head_content = ob_get_clean(); // head内容を取得
				ob_end_clean();
			}, PHP_INT_MAX - 2);

			// for debug bodyの最後をキャプチャする
/*
			add_action('wp_footer', function () {
				$neohp_all_content = ob_get_clean(); // head内容を取得
				ob_end_clean();
				$neohp_all_content = str_replace('> <', '><', $neohp_all_content);
				$neohp_all_content = str_replace('><', ">\n<", $neohp_all_content);
				echo '<textarea>' . esc_html($neohp_all_content) . '</textarea>';
			}, PHP_INT_MAX);
*/
			// リダイレクト確認
			add_action('wp_head', array($this, 'neohp_check_and_redirect'), PHP_INT_MAX - 1);

			// 本物のコンテンツ
			add_action('wp_head', function () {
				$head = $this->neohp_head_content;
//				$head = $this->replace_image_urls($head);
				if( ! $this->neohp_func->login() ) {
					if ( isset($_COOKIE['nonce']) && $this->neohp_func->verify_short_nonce($_COOKIE['nonce'], 'neUrl')) {
					} else {
//						exit;
					}
				}
				$this->neohp_func->head_echo($head);
//echo $head;
			}, PHP_INT_MAX);
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
					wp_die(esc_html(__('画像が見つかりません', 'neo-html-protector')));
				}

				// 必要に応じて、適切なコンテンツタイプを設定
				header('Content-Type: ' . $mine); // 画像の形式に合わせて自動設定
				
				// 画像を出力して転送
				global $wp_filesystem;

				if ( ! function_exists( 'WP_Filesystem' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				WP_Filesystem();

				if ( $wp_filesystem->exists( $image_path ) ) {
					$mime_type = wp_check_filetype( $image_path )['type'];
					if ( $mime_type ) {
						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						// This is image file (binary)
						header( 'Content-Type: ' . $mime_type );
						header( 'Content-Length: ' . $wp_filesystem->size( $image_path ) );
						echo $wp_filesystem->get_contents( $image_path );
						// phpcs:enable
					}
				}

				$user_ip = $this->neohp_func->get_user_ip();
				// 一時的なデータベースも削除
				$this->neohp_database->delete_view_source(
					array( 'ip' => $user_ip)
				);

				exit; // その後の処理を停止
			} else {
				// 画像が見つからない場合の処理（404など）
				wp_die(esc_html(__('画像が見つかりません', 'neo-html-protector')));
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

		// 言語を取得
		$lang = get_bloginfo('language');


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

			// アスキーアートの追加
			if(get_option('view_source_alert_asciiart', '0') !== '0') {
				$protectmsg = $warning_ascii_art[get_option('view_source_alert_asciiart', '0')] . "\n\n" . $protectmsg;
			}

			$html .= "<!--\n\n" . $protectmsg . "\n\n-->";
		}
		$html .= '<script>';
		$html .= 'var neUrl="' . $neo_encoded_url . '";';
		$html .= 'document.cookie="' . $cookie_name . '="+neUrl+";max-age=9;path=/";';

		$nonce = $this->neohp_func->create_short_nonce('neUrl');
		$html .= 'document.cookie="nonce=' . $nonce . ';max-age=9;path=/";';
		$html .= 'location.href=atob(neUrl);';
		$html .= '</script>';
echo $html;
//		$html = $this->replace_image_urls($this->neohp_head_content);
		$head = '';
		if(get_option('neohp_html_protect_head', '0') !== '0') {
			// Wordpressから <head>の部分のみ取得
			$head = $this->replace_image_urls($this->neohp_head_content);

			if(get_option('neohp_html_protect_head', '0') === '2') {
				$html .= $this->sanitize_output_head($head);
			} else {
				$html .= $this->sanitize_output_head_titleonly($head);
			}
		}


		$html .= '<noscript>';
		$html .= esc_html( __('このWebサイトはCookieとJavaScriptが有効でないと閲覧することはできません', 'neo-html-protector') );
		$html .= '</noscript></head></html>';

		$this->neohp_func->head_echo($head);

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

		if( ! $this->neohp_func->login() ) {
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
						$this->neohp_func->br_die($value);
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
		if( ! $this->neohp_func->login() ) {
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

	// HTML圧縮 titleオンリー
	protected function sanitize_output_head_titleonly($buffer) {
		if( ! $this->neohp_func->login() ) {
			$search = array(
				'#\s\/\>#s',			// XMLの /> を圧縮
				'#\>[^\S ]+#s', 		// タグの後の空白を削除
				'#[^\S ]+\<#s', 		// タグの前の空白を削除
				'#(\s)+#s', 			// 連続した空白を削除
				'#(\t)+#s', 			// 連続したタブを削除
				'#<!--[\s\S]*?-->#s',	// コメントを削除
				'#\t#s',				// 連続したタブを削除
				'#<style.*?>.*?<\/style>#s',  // styleは不要
				'#<script.*?>.*?<\/script>#s',  // scriptは不要
				'#<meta.*?>#s', 		//metaは不要
				'#<link.*?>#s', 		//linkは不要
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

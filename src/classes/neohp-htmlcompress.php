<?php
/**
 * Neo HTML Protector neohp_htmlcompress
 */

defined('ABSPATH') or die('Oh! No!');

$neohp_htmlcompress=new neohp_htmlcompress();
class neohp_htmlcompress {
	protected $neohp_func;
	protected $neohp_database;
	protected $mingif = 'data:image/gif;base64,R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7';

	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_database=new neohp_database();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if($this->neohp_func->is_image_bot() ) {
				$this->neohp_func->err403();
			}
		}

		if(get_option('neohp_deny_aibot', '0') === '1') {
			if($this->neohp_func->is_ai_bot() ) {
				$this->neohp_func->err403();
			}
		}

		if(get_option('neohp_htmlcompress', '1') == 1) {
			add_action('template_redirect', function() {
				$this->neohp_func->cachezero();
				ob_start(array($this, 'sanitize_output'));
			}, 1);
			add_action('wp_footer', function() {
				if ( ob_get_length() ) {
					ob_end_flush();
				}
			}, PHP_INT_MAX);

			// view-source検知のため、bodyの先頭にimgタグを生成
			if(get_option('neohp_htmlprotect', '0') === '0') {
				add_action('wp_body_open', function() {
//					$home = home_url(); // これを入れると勝手にbase64に展開される
					$home = '';
					$home .= '?neohp=compress&amp;nonce=' . $this->neohp_func->create_short_nonce('compress');

					echo '<img src="' . esc_url($home) . '" width="1" height="1" alt="" loading="eager" decoding="async">';

					// そしてview-source DBにinsert
					if($this->neohp_func->is_not_bot()) {
						// ユーザーのIPアドレスを取得
						$current_url = $this->neohp_func->get_current_url();
						$user_ip = $this->neohp_func->get_user_ip();
						$ua = $this->neohp_func->get_user_agent();

						// 一時的なログにview-sourceの検出情報を保存
						$wpdb = $this->neohp_database->create_view_source();

						// テーブルに保存
						$this->neohp_database->insert_view_source(
							$user_ip, $current_url, 'view-source', $ua
						);
					}
				}, 0);

				add_action('template_redirect', function() {
					// ここで一時的なログからデータベースに移動する
					$this->movedatabase();

					if (isset($_GET['neohp']) && $_GET['neohp'] === 'compress') {
						if($this->neohp_func->verify_short_nonce($_GET['nonce'], 'compress') ) {
							$this->neohp_func->cachezero();
							header('Content-Type: image/gif');
							$mingif_array=explode(',', $this->mingif);
							echo base64_decode($mingif_array[1]);
							exit;
						}
						wp_die(__('不正なリクエストです', 'neo_html_protector' ) );
					}
				}, 0);
			}
		}
	}

	public function movedatabase() {
		global $wpdb;

		// 一時的なログにview-sourceの検出情報を保存
		$wpdb = $this->neohp_database->create_view_source();

		$table_name = $wpdb->prefix . 'view_source_log';

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			"
			SELECT * FROM $table_name
			WHERE timestamp <= NOW() - INTERVAL 10 SECOND
			"
		);
		// phpcs:enable

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

	// HTML圧縮
	protected function sanitize_output($buffer) {
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
				'',
				'',
			);
			$buffer = preg_replace($search, $replace, $buffer);
			$buffer = preg_replace($search, $replace, $buffer);

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

			// <!doctype html>の前に警告メッセージを表示する
			require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

			if(get_option('neohp_htmlprotect_message', $neohp_htmlprotect_default ) !== '') {
				$protectmsg=esc_html(get_option('neohp_htmlprotect_message', $neohp_htmlprotect_default ) );
				$ua = $this->neohp_func->get_user_agent();
				$current_url = $this->neohp_func->get_current_url();
				$user_ip = $this->neohp_func->get_user_ip();

				$protectmsg = str_replace('$IP', $user_ip, $protectmsg);
				$protectmsg = str_replace('$URL', $current_url, $protectmsg);
				$protectmsg = str_replace('$KEY', "view-source:$current_url", $protectmsg);
				$protectmsg = str_replace('$UA', $ua, $protectmsg);
				$protectmsg = str_replace('\\n', "\n", $protectmsg);

				// アスキーアートの追加
				if(get_option('neohp_view_source_alert_asciiart', '0') !== '0') {
					$protectmsg = $warning_ascii_art[get_option('neohp_view_source_alert_asciiart', '0')] . "\n\n" . $protectmsg;
				}

				$buffer = "<!--\n\n" . $protectmsg . "\n\n-->" . $buffer;
			}
		}
		return $buffer;
	}
}

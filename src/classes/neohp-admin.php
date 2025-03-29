<?php
/**
 * Neo HTML Protector admin
 */

if (is_admin()) {
	$neohp_admin = new neohp_admin();
}

class neohp_admin {
	public function __construct() {
		add_action('admin_menu', array($this, 'add_admin_menu'));
	}

	function getselect($name, $selected, ...$options) {
		$html = "<select name=\"{$name}\">\n";
		
		foreach ($options as $option) {
			if (is_array($option)) {
				// 配列になっている場合は implode で文字列化
				foreach ($option as $opt) {
					list($value, $label) = explode('=', $opt, 2);
					$isSelected = ($value == $selected) ? ' selected' : '';
					$html .= "<option value=\"{$value}\"{$isSelected}>{$label}</option>\n";
				}
			} else {
				list($value, $label) = explode('=', $option, 2);
				$isSelected = ($value == $selected) ? ' selected' : '';
				$html .= "<option value=\"{$value}\"{$isSelected}>{$label}</option>\n";
			}
		}

		$html .= "</select>\n";
		
		return $html;
	}

	public function add_admin_menu() {
	// adminメニュー追加
		add_options_page(
			'Neo HTML Protector設定',	// ページタイトル
			'Neo HTML Protector',		// メニュータイトル
			'manage_options',			// 権限
			'neohp-settings',			// スラッグ
			array($this, 'render_neohp_settings_page'),	// コールバック
		);

		// 設定を登録
		add_action('admin_init', function() {
			// デバッグモードのAlert メッセージ
			register_setting('neohp_message_group', 'neohp_debugmode_message');
			add_settings_field(
				'neohp_debugmode_message',
				__('デバッグモード、コンソールの警告メッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_debugmode_message', $neohp_debugmode_default );
					echo '<input type="text" name="neohp_debugmode_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// 右クリックのAlert メッセージ
			register_setting('neohp_message_group', 'neohp_rightclick_message');
			add_settings_field(
				'neohp_rightclick_message',
				__('右クリックの警告メッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_rightclick_message', $neohp_rightclick_default );
					echo '<input type="text" name="neohp_rightclick_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// 印刷のAlert メッセージ
			register_setting('neohp_message_group', 'neohp_printout_message');
			add_settings_field(
				'neohp_printout_message',
				__('印刷、PDF保存の警告メッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_printout_message', $neohp_printout_default );
					echo '<input type="text" name="neohp_printout_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// 保存のAlert メッセージ
			register_setting('neohp_message_group', 'neohp_save_message');
			add_settings_field(
				'neohp_save_message',
				__('印刷、PDF保存の警告メッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_save_message', $neohp_save_default );
					echo '<input type="text" name="neohp_save_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// コピー、カットのメッセージ
			register_setting('neohp_message_group', 'neohp_copycut_message');
			add_settings_field(
				'neohp_copycut_message',
				__('コピー・カットした時に表示するメッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_copycut_message', $neohp_copycut_default );
					echo '<input type="text" name="neohp_copycut_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// HTMLソースのメッセージ
			register_setting('neohp_message_group', 'neohp_htmlsource_message');
			add_settings_field(
				'neohp_htmlsource_message',
				__('HTMLソース表示時に表示するメッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_htmlsource_message', $neohp_htmlsource_default );
					echo '<input type="text" name="neohp_htmlsource_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// HTML保護時のメッセージ
			register_setting('neohp_message_group', 'neohp_htmlprotect_message');
			add_settings_field(
				'neohp_htmlprotect_message',
				__('HTML難読化・保護時にソースの先頭に表示するメッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_htmlprotect_message', $neohp_htmlprotect_default );
					echo '<input type="text" name="neohp_htmlprotect_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			add_settings_field(
				'neohp_nonceerror_message',
				__('HTML保護時に一度限りの認証トークンが異常の場合に表示するメッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_nonceerror_message', $neohp_nonceerror_default );
					echo '<input type="text" name="neohp_nonceerror_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// no cookie, no js時のメッセージ
			register_setting('neohp_message_group', 'neohp_nocookienojs_message');
			add_settings_field(
				'neohp_nocookienojs_message',
				__('CookieやJavascriptが有効でない場合に表示するメッセージ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_nocookienojs_message', $neohp_nocookienojs_default );
					echo '<input type="text" name="neohp_nocookienojs_message" value="' . esc_html( $value ) . '" class="regular-text">';
					echo '<br>' .esc_html( __('実際にはJavascriptが無効時のみ表示されます', 'neo-html-protector') );
				},
				'neohp-message-settings',
				'neohp_message_section'
			);

			// 画像がダウンロードされた時のメッセージ
			register_setting('neohp_message_group', 'neohp_imagedownload_message');
			add_settings_field(
				'neohp_imagedownload_message',
				__('画像保護時に画像がダウンロードされ、その画像に表示するメッセージ 英語のみ', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = get_option('neohp_imagedownload_message', $neohp_imagedownload_default );
					echo '<input type="text" name="neohp_imagedownload_message" value="' . esc_html( $value ) . '" class="regular-text">';
				},
				'neohp-message-settings',
				'neohp_message_section'
			);




			// HTML圧縮
			register_setting('neohp_basic_group', 'neohp_htmlcompress');
			add_settings_field(
				'neohp_htmlcompress',
				__('HTML難読化 (圧縮)', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_htmlcompress', '1'));
					echo wp_kses( $this->getselect("neohp_htmlcompress", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('一般的なHTML圧縮です、難読化解除のサイトもあります', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// HTML保護
			register_setting('neohp_basic_group', 'neohp_htmlprotect');
			add_settings_field(
				'neohp_htmlprotect',
				__('HTML保護', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_htmlprotect', '0'));
					echo wp_kses( $this->getselect("neohp_htmlprotect", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );

					echo '<br>' .esc_html( __('HTML圧縮以上に最小限のHTMLしか出力せず、BODYタグ内の内容が全く出力されなくなります', 'neo-html-protector') );
					echo '<br>' .esc_html( __('view-source:の動作をされた時の記録もします', 'neo-html-protector') );
					echo '<br>' . esc_html( __('有効化した時は必ずリダイレクトが発生するため、SEOが落ちるかもしれません', 'neo-html-protector') );
					echo '<br>' . esc_html( __('若干デザインが変わる可能性があります', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// 画像保護
			register_setting('neohp_basic_group', 'neohp_imageprotect');
			add_settings_field(
				'neohp_imageprotect',
				__('画像の保護', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_imageprotect', '0'));
					echo wp_kses( $this->getselect("neohp_imageprotect", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('add_filterを使用して画像を保護', 'neo-html-protector')
						, '2=' . __('wp_head～wp_footerの間をキャプチャして画像を保護', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );

					echo '<br>' .esc_html( __('画像をダウンロードから保護します', 'neo-html-protector') );
					echo '<br>' .esc_html( __('画像データを保護した時にはほぼ完全なるダウンロードを阻止し、完全なるワンタイムURLを発行し、セッションに保存されたトークンで認証し、phpから画像を表示します、その為ほぼ完全な画像盗用を防ぎます', 'neo-html-protector') );
					echo '<br>';
					echo '<br>' .esc_html( __('画像データを保護した時には画像のキャッシュが効かないため、次回訪問時にサイトの読み込みが遅くなるため、SEOが落ちるかもしれません', 'neo-html-protector') );
					echo '<br>' .esc_html( __('画像データを保護した時には、データベースの負荷が高くなる可能性があります', 'neo-html-protector') );
					echo '<br>' .esc_html( __('add_filterを使用した方式はimgタグの発行時にフィルタリングを行い、wp_head～wp_footerを使用した方式はコンテンツ内のimgタグについてすべて処理します。テーマによっては正しく動作しません', 'neo-html-protector') );
					echo '<br>' .esc_html( __('なお、アイキャッチに指定した画像はOGPとして拡散されますので保護できません。どうしても保護すべき場合は高度な設定でHTML保護時のHEADタグの出力の選択を変更してください', 'neo-html-protector') );
					echo '<br>' .esc_html( __('OS標準搭載のスクリーンショット機能を保護することはできません', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// 画像URL保護
			register_setting('neohp_basic_group', 'neohp_imageprotectjs');
			add_settings_field(
				'neohp_imageprotectjs',
				__('画像URLの保護', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_imageprotectjs', '0'));
					echo wp_kses( $this->getselect("neohp_imageprotectjs", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '2=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );

					echo '<br>' .esc_html( __('画像の保護と組み合わせて使用します、単体では意味がありません', 'neo-html-protector') );
					echo '<br>' .esc_html( __('JavaScriptにより遅延読まれますのでSEOに影響があります', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// HTMLソース表示時の警告の方法
			register_setting('neohp_basic_group', 'view_source_alert_asciiart');
			add_settings_field(
				'view_source_alert_asciiart',
				__('HTML難読化・保護時のHTMLソースコードのアスキーアートの出力', 'neo-html-protector'),

				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$warning_ascii_art_array=[];
					foreach ($warning_ascii_art	 as $k => $v) {
						array_push($warning_ascii_art_array, "$k=$k (" . strlen($v) . ' bytes)' );
					}
					$value = esc_html(get_option('view_source_alert_asciiart', '0'));
					echo wp_kses( $this->getselect("view_source_alert_asciiart", $value
						, '0=' . __('表示なし', 'neo-html-protector')
						, $warning_ascii_art_array
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('HTMLソース表示をした時に警告の意思を示すアスキーアートを表示します', 'neo-html-protector') );
					echo '<br>' . esc_html( __('ログインしていないブラウザーでソース表示を行って確認して下さい', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);


			// F12
			register_setting('neohp_basic_group', 'neohp_alert_f12');
			add_settings_field(
				'neohp_alert_f12',
				'F12 (' . __('デバッグモード', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_f12', '2'));
					echo wp_kses( $this->getselect("neohp_alert_f12", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メニューからは操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Ctrl+Shift+I
			register_setting('neohp_basic_group', 'neohp_alert_i');
			add_settings_field(
				'neohp_alert_i',
				'Ctrl+Shift+I (' . __('デバッグモード', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_i', '2'));
					echo wp_kses( $this->getselect("neohp_alert_i", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メニューからは操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Ctrl+Shift+J
			register_setting('neohp_basic_group', 'neohp_alert_j');
			add_settings_field(
				'neohp_alert_j',
				'Ctrl+Shift+J (' . __('ブラウザーコンソール', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_j', '2'));
					echo wp_kses( $this->getselect("neohp_alert_j", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('事実上デバッグモードから操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Ctrl+U
			register_setting('neohp_basic_group', 'neohp_alert_u');
			add_settings_field(
				'neohp_alert_u',
				'Ctrl + U (' . __('HTMLソース表示', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_u', '2'));
					echo wp_kses( $this->getselect("neohp_alert_u", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('view-source:から始まるURLを入力すれば操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Ctrl+P
			register_setting('neohp_basic_group', 'neohp_alert_p');
			add_settings_field(
				'neohp_alert_p',
				'Ctrl+P (' . __('印刷', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_p', '1'));
					echo wp_kses( $this->getselect("neohp_alert_p", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メニューからは操作できてしまいます', 'neo-html-protector') );
					echo '<br>' . esc_html( __('印刷阻止をするもものの、ブラウザによってはうまく動作しません', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Ctrl+S
			register_setting('neohp_basic_group', 'neohp_alert_s');
			add_settings_field(
				'neohp_alert_s',
				'Ctrl+S (' . __('ページ保存', 'neo-html-protector') . ')',
				function() {
					$value = esc_html(get_option('neohp_alert_s', '2'));
					echo wp_kses( $this->getselect("neohp_alert_s", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メニューからは操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Right Click
			register_setting('neohp_basic_group', 'neohp_alert_r');
			add_settings_field(
				'neohp_alert_r',
				__('右クリック', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_r', '2'));
					echo wp_kses( $this->getselect("neohp_alert_r", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('アドオンがインストールされていると操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// Copy/Cut
			register_setting('neohp_basic_group', 'neohp_alert_c');
			add_settings_field(
				'neohp_alert_c',
				__('コピー・カット', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_c', '1'));
					echo wp_kses( $this->getselect("neohp_alert_c", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('あまりこのイベントに遭遇することはありません', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// selection
			register_setting('neohp_basic_group', 'neohp_alert_t');
			add_settings_field(
				'neohp_alert_t',
				__('テキスト選択', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_t', '1'));
					echo wp_kses( $this->getselect("neohp_alert_t", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('アドオンがインストールされていると操作できてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// debugger
			register_setting('neohp_basic_group', 'neohp_alert_d');
			add_settings_field(
				'neohp_alert_d',
				__('デバッガー妨害', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_d', '1'));
					echo wp_kses( $this->getselect("neohp_alert_d", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('デバッグモードの使い勝手を少し悪くします、ブラウザによってはこの挙動が止められてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// 転送先URL
			register_setting('neohp_basic_group', 'neohp_redirect_url');
			add_settings_field(
				'neohp_redirect_url',
				__('リダイレクトするURL', 'neo-html-protector'),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_url(get_option('neohp_redirect_url', $neohp_redirect_default));
					echo '<input type="url" name="neohp_redirect_url" value="' . esc_html( $value ) . '" class="regular-text">';
					echo '<br>' . esc_html( __('利用規約などのページに転送すると良いでしょう', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// debugger
			register_setting('neohp_basic_group', 'neohp_alert_d');
			add_settings_field(
				'neohp_alert_d',
				__('デバッガー妨害', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_d', '1'));
					echo wp_kses( $this->getselect("neohp_alert_d", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('デバッグモードの使い勝手を少し悪くします、ブラウザによってはこの挙動が止められてしまいます', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// ソース表示を許可する権限
			register_setting('neohp_basic_group', 'neohp_islogin');
			add_settings_field(
				'neohp_islogin',
				__('ソース表示を許可する権限', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_islogin', 'admin'));
					echo wp_kses( $this->getselect("neohp_islogin", $value
						, 'admin=' . __('ADMINログイン時のみ通常のソース出力', 'neo-html-protector')
						, 'user=' . __('USERログインで通常のソース出力', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('通常のHTML出力を管理者のみかユーザーログインかを選択します', 'neo-html-protector') );
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// HTML保護時のheadの出力
			register_setting('neohp_advanced_group', 'neohp_html_protect_head');
			add_settings_field(
				'neohp_html_protect_head',
				__('HTML保護時のHEADタグの出力', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_html_protect_head', '2'));
					echo wp_kses( $this->getselect("neohp_html_protect_head", $value
						, '0=' . __('出力しない', 'neo-html-protector')
						, '1=' . __('TITLEタグのみ', 'neo-html-protector')
						, '2=' . __('WordpressのHEADタグより取得', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('企業用途の会員専用ページや社内ページなどでは「出力しない」、「TITLEタグのみ」で良いでしょう', 'neo-html-protector') );
					echo '<br>' . esc_html( __('WordpressのHEADタグから取得しないと、SNSのシェアでOGP画像が表示されません', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// 画像botを避ける
			register_setting('neohp_advanced_group', 'neohp_deny_imagebot');
			add_settings_field(
				'neohp_deny_imagebot',
				__('画像botをアクセス禁止にする', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_deny_imagebot', '0'));
					echo wp_kses( $this->getselect("neohp_deny_imagebot", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('画像botをHTMLに対し避けることにより、画像検索から直接リンクされることによって守れなかったコンテンツを守ることができます', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// alertメッセージの言語
			register_setting('neohp_advanced_group', 'neohp_alert_message_lang');
			add_settings_field(
				'alert_message_lang',
				__('alertメッセージを表示する言語', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_message_lang', '0'));
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$lang_array=[];
					foreach ($neohp_lang as $k => $v) {
						array_push($lang_array, "$k=$k=$v");
					}

					echo wp_kses( $this->getselect("neohp_alert_message_lang", $value
						, '0=' . __('Wordpressの言語', 'neo-html-protector')
						, '1=' . __('ブラウザの設定言語', 'neo-html-protector')
						, $lang_array
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メッセージをカスタム設定されている場合は言語を変更できません', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// view-sourceメッセージの言語
			register_setting('neohp_advanced_group', 'neohp_view-source_message_lang');
			add_settings_field(
				'view-source_message_lang',
				__('view-sourceメッセージを表示する言語', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_view-source_message_lang', '0'));
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$lang_array=[];
					foreach ($neohp_lang as $k => $v) {
						array_push($lang_array, "$k=$k=$v");
					}

					echo wp_kses( $this->getselect("neohp_view-source_message_lang", $value
						, '0=' . __('Wordpressの言語', 'neo-html-protector')
						, '1=' . __('ブラウザの設定言語', 'neo-html-protector')
						, $lang_array
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('メッセージをカスタム設定されている場合は言語を変更できません', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// nonceのbit数
			register_setting('neohp_advanced_group', 'neohp_nonce_expire');
			add_settings_field(
				'neohp_nonce_expire',
				__('一時使用トークンの有効期限', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_nonce_expire', '20'));
					echo wp_kses( $this->getselect("neohp_nonce_expire", $value
						, '1='  . __('1分', 'neo-html-protector')
						, '2='  . __('2分', 'neo-html-protector')
						, '3='  . __('3分', 'neo-html-protector')
						, '4='  . __('4分', 'neo-html-protector')
						, '5='  . __('5分', 'neo-html-protector')
						, '6='  . __('6分', 'neo-html-protector')
						, '7='  . __('7分', 'neo-html-protector')
						, '8='  . __('8分', 'neo-html-protector')
						, '9='  . __('9分', 'neo-html-protector')
						, '10='  . __('10分', 'neo-html-protector')
						, '15='  . __('15分', 'neo-html-protector')
						, '20='  . __('20分', 'neo-html-protector')
						, '25='  . __('25分', 'neo-html-protector')
						, '30='  . __('30分', 'neo-html-protector')
						, '35='  . __('35分', 'neo-html-protector')
						, '40='  . __('40分', 'neo-html-protector')
						, '45='  . __('45分', 'neo-html-protector')
						, '50='  . __('50分', 'neo-html-protector')
						, '55='  . __('55分', 'neo-html-protector')
						, '60='  . __('1時間', 'neo-html-protector')
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('画像URL情報の有効期限を設定します', 'neo-html-protector') );
					echo '<br>' . esc_html( __('一度読み込まれると画像URL情報と一時使用トークンは無効化されます', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// nonceのbit数
			register_setting('neohp_advanced_group', 'neohp_nonce_bits');
			add_settings_field(
				'neohp_nonce_bits',
				__('一時使用トークンのビット数', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_nonce_bits', '32'));
					echo wp_kses( $this->getselect("neohp_nonce_bits", $value
						, '16=' . '128bits (16bytes)'
						, '32=' . '256bits (32bytes)'
						, '48=' . '384bits (48bytes)'
						, '64=' . '512bits (64bytes)'
						, '96=' . '768bits (96bytes)'
						, '128=' . '1024bits (128bytes)'
						, '192=' . '1536bits (192bytes)'
						, '256=' . '2048bits (256bytes)'
						, '384=' . '3072bits (384bytes)'
						, '512=' . '4096bits (512bytes)'
						, '768=' . '6144bits (768bytes)'
						, '1024=' . '8192bits (1024bytes)'
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('画像URL情報を暗号化するのに一時使用トークンをパスワードとして使用しますが、その強度を指定します', 'neo-html-protector') );
					echo '<br>' . esc_html( __('ドロップダウンメニューの下に表示される選択肢にいくにつれて強度が強くなるものの、サーバーへの負荷が高くなり、HTML転送量が多くなることがあります', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// hashのbit数
			register_setting('neohp_advanced_group', 'neohp_hash_bits');
			add_settings_field(
				'neohp_hash_bits',
				__('ハッシュ化アルゴリズム', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_hash_bits', 'sha256'));
					echo wp_kses( $this->getselect("neohp_hash_bits", $value
						, 'sha224=' . 'sha2 224bits'
						, 'sha256=' . 'sha2 256bits'
						, 'sha384=' . 'sha2 384bits'
						, 'sha512=' . 'sha2 512bits'
						, 'sha3-224=' . 'sha3 224bits'
						, 'sha3-256=' . 'sha3 256bits'
						, 'sha3-384=' . 'sha3 384bits'
						, 'sha3-512=' . 'sha3 512bits'
					), [ 'select'=>['name'=>true], 'option'=>['value'=>true, 'selected'=>true] ] );
					echo '<br>' . esc_html( __('画像URL情報を暗号化するのに一時使用トークンをパスワードとして使用しますが、その時ハッシュ化が必要です、その強度を設定します', 'neo-html-protector') );
					echo '<br>' . esc_html( __('ドロップダウンメニューの下に表示される選択肢にいくにつれて強度が強くなるものの、サーバーへの負荷が高くなります', 'neo-html-protector') );
				},
				'neohp-advanced-settings',
				'neohp_advanced_section'
			);

			// セクションの追加
			add_settings_section(
				'neohp_basic_section',
				__('基本設定', 'neo-html-protector'),
				function() {
				},
				'neohp-settings'
			);

			add_settings_section(
				'neohp_advanced_section',
				__('高度な設定', 'neo-html-protector'),
				function() {
				},
				'neohp-advanced-settings'
			);

			add_settings_section(
				'neohp_allclear_section',
				__('初期設定に戻す', 'neo-html-protector'),
				function() {
				},
				'neohp-allclear-settings'
			);

			// セクションの追加
			add_settings_section(
				'neohp_message_section',
				__('メッセージの設定', 'neo-html-protector'),
				function() {
					echo 
	  esc_html( __('右クリックやソースコード表示時に転送する URL を設定します', 'neo-html-protector') ) . '<br><br>'
	. esc_html( __('警告メッセージにはHTMLは使用できません', 'neo-html-protector') ) 	. '<br><br>'
	. esc_html( __('この画面で設定すると、高度な設定タブにある言語設定が無視されます', 'neo-html-protector' ) ) . '<br><br>'
	. esc_html( __('以下の文字列が使用できます', 'neo-html-protector') )
	. '<table><tr><td>\n</td><td>' . esc_html( __('改行', 'neo-html-protector') )
	. '</td></tr><tr><td>$IP</td><td>' . esc_html( __('IPアドレス', 'neo-html-protector') )
	. '</td></tr><tr><td>$UA</td><td>' . esc_html( __('ユーザーエージェント', 'neo-html-protector') )
	. '</td></tr><tr><td>$URL</td><td>' . esc_html( 'URL')
	. '</td></tr><tr><td>$KEY</td><td>' . esc_html( __('押下されたキー', 'neo-html-protector') )
	. '</td></tr></table>';
				},
				'neohp-message-settings'
			);
		});

	}

	// 設定ページの内容を表示
	public function render_neohp_settings_page() {
		// $_GET['tab'] の存在確認と初期値設定
		$active_tab = filter_input(INPUT_GET, 'tab') ?? 'general';
		?>
		<div class="wrap">
			<h1><?php echo esc_html( __('Neo HTML Protector 設定', 'neo-html-protector') ) ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="?page=neohp-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( __('基本設定', 'neo-html-protector') ) ?></a>
				<a href="?page=neohp-settings&tab=message" class="nav-tab <?php echo $active_tab === 'message' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( __('メッセージの設定', 'neo-html-protector') ) ?></a>
				<a href="?page=neohp-settings&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( __('高度な設定', 'neo-html-protector') ) ?></a>
				<a href="?page=neohp-settings&tab=clear" class="nav-tab <?php echo $active_tab === 'clear' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( __('初期設定に戻す', 'neo-html-protector') ) ?></a>
				<a href="?page=neohp-settings&tab=about" class="nav-tab <?php echo $active_tab === 'about' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( __('このプラグインについて', 'neo-html-protector') ) ?></a>
			</h2>
			<form method="post" action="options.php">
				<?php
				if ($active_tab === 'general') {
					$this->general_settings();
				} elseif ($active_tab === 'message') {
					$this->message_settings();
				} elseif ($active_tab === 'advanced') {
					$this->advanced_settings();
				} elseif ($active_tab === 'clear') {
					$this->all_clear();
				} elseif ($active_tab === 'about') {
					$this->about_page();
				}
				?>
			</form>
		</div>
		<?php
	}

	function general_settings() {
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php
				settings_fields('neohp_basic_group');
				do_settings_sections('neohp-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	function message_settings() {
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php
				settings_fields('neohp_message_group');
				do_settings_sections('neohp-message-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	function advanced_settings() {
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php
				settings_fields('neohp_advanced_group');
				do_settings_sections('neohp-advanced-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	function all_clear() {
		?>
		<div class="wrap">
			<p><?php echo esc_html( __('データをすべてクリアするには、プラグインをアンインストールしてから、インストールしなおして下さい', 'neo-html-protector') ) ?></p>
		</div>
		<?php
	}

	// このプラグインについてタブ
	private function about_page() {
		?>
<h2><?php echo esc_html( __('Neo HTML Protectorについて', 'neo-html-protector') ) ?></h2>
<p><?php echo esc_html( __('このプラグインはあなたのWordpressから出力されるHTMLや画像等を保護し、コンテンツの不正利用から守ることを目的としています。', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('コンテンツを完璧に守ることはできません。しかし可能な限りの方法において警告し、もし何かがあった場合に備えて準備することにより国で定められている方法で解決することができるはずです。', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('もちろん、利用規約やサイトポリシーを充実させる必要があります。このプラグインは利用規約生成アプリではありませんが、そのヒントを与えることができます。', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('開発者: 夜桜　なの', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('バージョン', 'neo-html-protector') ) ?>: <?php echo esc_html( NEOHP_VERSION ) ?> <?php esc_html( __('ビルド', 'neo-html-protector') ) ?>: <?php echo esc_html( NEOHP_BUILD ) ?></p>
<p><?php echo esc_html( __('サポートページ', 'neo-html-protector') ) ?>: <a target="_blank" href="https://support.773.moe/neo-html-protector">https://support.773.moe/neo-html-protector</a></p>
<p><a target="_blank" href="https://support.773.moe/donate/"><img src="<?php echo esc_html( NEOHP_IMG_URL ) ?>/nano.gif" alt="Donate Button" width="240" style="border-radius: 30px"></a></p>
		<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscape
		// This is an officially provided embedded HTML, and is unlikely to be processed from outside
echo '<p><script type="text/javascript" src="https://embed.nicovideo.jp/watch/sm44565088/script?w=640&h=360"></script><br><a target="_blank" href="https://www.nicovideo.jp/watch/sm44565088">[' . esc_html(__('ISISちゃん', 'neo-html-protector')) . '] Give Me Merorin 1.6 ' . __('Miss. 裏まにら氏歌唱', 'neo-html-protector') . '</a></p>';
		// phpcs:enable
		?>
<h2><?php echo esc_html( __('支援のお願い', 'neo-html-protector') ) ?></h2>
<p><?php echo esc_html( __('Neo HTML Protectorをご利用いただき、ありがとうございます！', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('本プラグインの開発と維持には多くの時間と知恵の絞りだしがかかっており、引き続き改善と更新を行っていくための資金を集めるために、もしご支援いただける方がいれば、寄付をお願いできればと思います。', 'neo-html-protector') ) ?></p>
<h3><?php echo esc_html( __('寄付について', 'neo-html-protector') ) ?></h3>
<p><?php echo esc_html( __('寄付は任意であり、強制ではありません。プラグインを無料でご利用いただけるように、どなたでも利用可能です。しかし、開発を継続するためにはご支援が大変ありがたく、少しでもご協力いただけると嬉しいです。', 'neo-html-protector') ) ?></p>

<p><?php echo esc_html( __('ご寄付は、下記のリンクから行っていただけます。日本の方であれば無料で寄付をすることも可能で、更にはアマゾンギフト券で気軽に寄付をすることが可能です。', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('寄付先', 'neo-html-protector') ) ?>:<a target="_blank" href="https://support.773.moe/donate">https://support.773.moe/donate</a></p>
<p><?php echo esc_html( __('皆様のご支援があれば、今後も素晴らしいアップデートをお届けできるよう頑張ります！', 'neo-html-protector') ) ?></p>
<p><?php echo esc_html( __('ご支援いただけることに感謝し、今後ともよろしくお願いいたします。', 'neo-html-protector') ) ?></p>
		<?php
	}
}


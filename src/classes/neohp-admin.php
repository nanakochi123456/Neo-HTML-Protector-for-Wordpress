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

	protected function getselect($name, $selected, ...$options) {
		$html = "<select name=\"{$name}\">\n";
		
		foreach ($options as $option) {
			list($value, $label) = explode('=', $option, 2);
			$isSelected = ($value == $selected) ? ' selected' : '';
			$html .= "<option value=\"{$value}\"{$isSelected}>{$label}</option>\n";
		}
		
		$html .= "</select>\n";
		
		return $html;
	}

	public function add_admin_menu() {
	// adminメニュー追加
		add_options_page(
			'Neo HTML Protector設定',
			'Neo HTML Protector',
			'manage_options',
			'neohp-settings',
			array($this, 'render_neohp_settings_page')
		);

		// 設定を登録
		add_action('admin_init', function() {
			// デバッグモードのAlert メッセージ
			register_setting('neohp_settings_group', 'neohp_debugmode_message');
			add_settings_field(
				'neohp_debugmode_message',
				__('デバッグモード、コンソールの警告メッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_debugmode_message', $neohp_debugmode_default ));
					echo '<input type="text" name="neohp_debugmode_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 右クリックのAlert メッセージ
			register_setting('neohp_settings_group', 'neohp_rightclick_message');
			add_settings_field(
				'neohp_rightclick_message',
				__('右クリックの警告メッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_rightclick_message', $neohp_rightclick_default ));
					echo '<input type="text" name="neohp_rightclick_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 印刷のAlert メッセージ
			register_setting('neohp_settings_group', 'neohp_printout_message');
			add_settings_field(
				'neohp_printout_message',
				__('印刷、PDF保存の警告メッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_printout_message', $neohp_printout_default ));
					echo '<input type="text" name="neohp_printout_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// コピー、カットのメッセージ
			register_setting('neohp_settings_group', 'neohp_copycut_message');
			add_settings_field(
				'neohp_copycut_message',
				__('コピー・カットした時に表示するメッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_copycut_message', $neohp_copycut_default ));
					echo '<input type="text" name="neohp_copycut_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTMLソースのメッセージ
			register_setting('neohp_settings_group', 'neohp_htmlsource_message');
			add_settings_field(
				'neohp_htmlsource_message',
				__('HTMLソース表示時に表示するメッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_htmlsource_message', $neohp_htmlsource_default ));
					echo '<input type="text" name="neohp_htmlsource_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML保護時のメッセージ
			register_setting('neohp_settings_group', 'neohp_htmlprotect_message');
			add_settings_field(
				'neohp_htmlprotect_message',
				__('HTML保護時にソースの先頭に表示するメッセージ', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_html(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ));
					echo '<input type="text" name="neohp_htmlprotect_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 転送先URL
			register_setting('neohp_settings_group', 'neohp_redirect_url');
			add_settings_field(
				'neohp_redirect_url',
				__('転送先URL', NEOHP_DOMAIN),
				function() {
					require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
					$value = esc_url(get_option('neohp_redirect_url', $neohp_redirect_default));
					echo '<input type="url" name="neohp_redirect_url" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML圧縮
			register_setting('neohp_settings_group', 'neohp_htmlcompress');
			add_settings_field(
				'neohp_htmlcompress',
				__('HTML難読化 (圧縮)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_htmlcompress', '1'));
					echo $this->getselect("neohp_htmlcompress", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('有効', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML保護
			register_setting('neohp_settings_group', 'neohp_htmlprotect');
			add_settings_field(
				'neohp_htmlprotect',
				__('HTML保護', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_htmlprotect', '0'));
					echo $this->getselect("neohp_htmlprotect", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('有効', NEOHP_DOMAIN)
					);

					echo __('有効化した時は必ずリダイレクトが発生するため、SEOが落ちるかもしれません', NEOHP_DOMAIN);
				},
				'neohp-settings',
				'neohp_section'
			);

			// F12
			register_setting('neohp_settings_group', 'neohp_alert_f12');
			add_settings_field(
				'neohp_alert_f12',
				__('F12 (デバッグモード)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_f12', '2'));
					echo $this->getselect("neohp_alert_f12", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+Shift+I
			register_setting('neohp_settings_group', 'neohp_alert_i');
			add_settings_field(
				'neohp_alert_i',
				__('Ctrl+Shift+I (デバッグモード)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_i', '2'));
					echo $this->getselect("neohp_alert_i", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+Shift+J
			register_setting('neohp_settings_group', 'neohp_alert_j');
			add_settings_field(
				'neohp_alert_j',
				__('Ctrl+Shift+J (コンソール)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_j', '2'));
					echo $this->getselect("neohp_alert_j", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+U
			register_setting('neohp_settings_group', 'neohp_alert_u');
			add_settings_field(
				'neohp_alert_u',
				__('Ctrl+U (HTMLソース表示)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_u', '2'));
					echo $this->getselect("neohp_alert_u", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+P
			register_setting('neohp_settings_group', 'neohp_alert_p');
			add_settings_field(
				'neohp_alert_p',
				__('Ctrl+P (印刷)', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_p', '1'));
					echo $this->getselect("neohp_alert_p", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
					echo __('印刷阻止をするもものの、ブラウザによってはうまく動作しません', NEOHP_DOMAIN);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Right Click
			register_setting('neohp_settings_group', 'neohp_alert_r');
			add_settings_field(
				'neohp_alert_r',
				__('右クリック', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_r', '2'));
					echo $this->getselect("neohp_alert_r", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Copy/Cut
			register_setting('neohp_settings_group', 'neohp_alert_c');
			add_settings_field(
				'neohp_alert_c',
				__('コピー・カット', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_c', '1'));
					echo $this->getselect("neohp_alert_c", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// selection
			register_setting('neohp_settings_group', 'neohp_alert_s');
			add_settings_field(
				'neohp_alert_s',
				__('テキスト選択', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_s', '1'));
					echo $this->getselect("neohp_alert_s", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害＋記録のみ', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// debugger
			register_setting('neohp_settings_group', 'neohp_alert_d');
			add_settings_field(
				'neohp_alert_d',
				__('デバッガー妨害', NEOHP_DOMAIN),
				function() {
					$value = esc_html(get_option('neohp_alert_d', '1'));
					echo $this->getselect("neohp_alert_d", $value
						, '0=' . __('無効', NEOHP_DOMAIN)
						, '1=' . __('妨害', NEOHP_DOMAIN)
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// セクションの追加
			add_settings_section(
				'neohp_section',
				__('基本設定', NEOHP_DOMAIN),
				function() {
					echo 
	  __('右クリックやソースコード表示時に転送する URL を設定します', NEOHP_DOMAIN) . '<br><br>'
	. __('警告メッセージにはHTMLは使用できません', NEOHP_DOMAIN) . '<br><br>'
	. __('以下の文字列が使用できます', NEOHP_DOMAIN)
	. '<table><tr><td>\n</td><td>' . __('改行', NEOHP_DOMAIN)
	. '</td></tr><tr><td>$IP</td><td>' . __('IPアドレス', NEOHP_DOMAIN)
	. '</td></tr><tr><td>$URL</td><td>' . __('URL', NEOHP_DOMAIN)
	. '</td></tr><tr><td>$KEY</td><td>' . __('押下されたキー', NEOHP_DOMAIN)
	. '</td></tr></table>';
				},
				'neohp-settings'
			);
		});
	}

	// 設定ページの内容を表示
	public function render_neohp_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo __('Neo HTML Protector設定', NEOHP_DOMAIN) ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields('neohp_settings_group');
				do_settings_sections('neohp-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

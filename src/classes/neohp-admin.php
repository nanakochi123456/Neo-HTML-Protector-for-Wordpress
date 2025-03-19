<?php
/**
 * Neo HTML Protector admin
 */

$neohp_admin = new neohp_admin();
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
				_('デバッグモード、コンソールの警告メッセージ'),
				function() {
					$value = esc_html(get_option('neohp_debugmode_message', NEOHP_DEBUGMODE_DEFAULT ));
					echo '<input type="text" name="neohp_debugmode_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 右クリックのAlert メッセージ
			register_setting('neohp_settings_group', 'neohp_rightclick_message');
			add_settings_field(
				'neohp_rightclick_message',
				_('右クリックの警告メッセージ'),
				function() {
					$value = esc_html(get_option('neohp_rightclick_message', NEOHP_RIGHTCLICK_DEFAULT ));
					echo '<input type="text" name="neohp_rightclick_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 印刷のAlert メッセージ
			register_setting('neohp_settings_group', 'neohp_printout_message');
			add_settings_field(
				'neohp_printout_message',
				_('印刷、PDF保存の警告メッセージ'),
				function() {
					$value = esc_html(get_option('neohp_printout_message', NEOHP_PRINTOUT_DEFAULT ));
					echo '<input type="text" name="neohp_printout_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTMLソースのメッセージ
			register_setting('neohp_settings_group', 'neohp_htmlsource_message');
			add_settings_field(
				'neohp_htmlsource_message',
				_('HTMLソース表示時に表示するメッセージ'),
				function() {
					$value = esc_html(get_option('neohp_htmlsource_message', NEOHP_HTMLSOURCE_DEFAULT ));
					echo '<input type="text" name="neohp_htmlsource_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML保護時のメッセージ
			register_setting('neohp_settings_group', 'neohp_htmlprotect_message');
			add_settings_field(
				'neohp_htmlprotect_message',
				_('HTML保護時にソースの先頭に表示するメッセージ'),
				function() {
					$value = esc_html(get_option('neohp_htmlprotect_message', NEOHP_VIEWSOURCE_DEFAULT ));
					echo '<input type="text" name="neohp_htmlprotect_message" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// 転送先URL
			register_setting('neohp_settings_group', 'neohp_redirect_url');
			add_settings_field(
				'neohp_redirect_url',
				_('転送先URL'),
				function() {
					$value = esc_url(get_option('neohp_redirect_url', NEOHP_REDIRECT_DEFAULT));
					echo '<input type="url" name="neohp_redirect_url" value="' . $value . '" class="regular-text">';
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML圧縮
			register_setting('neohp_settings_group', 'neohp_htmlcompress');
			add_settings_field(
				'neohp_htmlcompress',
				_('HTML難読化 (圧縮)'),
				function() {
					$value = esc_html(get_option('neohp_htmlcompress', '1'));
					echo $this->getselect("neohp_htmlcompress", $value
						, '0=' . _('無効')
						, '1=' . _('有効')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// HTML保護
			register_setting('neohp_settings_group', 'neohp_htmlprotect');
			add_settings_field(
				'neohp_htmlprotect',
				_('HTML保護'),
				function() {
					$value = esc_html(get_option('neohp_htmlprotect', '0'));
					echo $this->getselect("neohp_htmlprotect", $value
						, '0=' . _('無効')
						, '1=' . _('有効')
					);

					echo _('有効化した時は必ずリダイレクトが発生するため、SEOが落ちるかもしれません');
				},
				'neohp-settings',
				'neohp_section'
			);

			// F12
			register_setting('neohp_settings_group', 'neohp_alert_f12');
			add_settings_field(
				'neohp_alert_f12',
				_('F12 (デバッグモード)'),
				function() {
					$value = esc_html(get_option('neohp_alert_f12', '2'));
					echo $this->getselect("neohp_alert_f12", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+Shift+I
			register_setting('neohp_settings_group', 'neohp_alert_i');
			add_settings_field(
				'neohp_alert_i',
				_('Ctrl+Shift+I (デバッグモード)'),
				function() {
					$value = esc_html(get_option('neohp_alert_i', '2'));
					echo $this->getselect("neohp_alert_i", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+Shift+J
			register_setting('neohp_settings_group', 'neohp_alert_j');
			add_settings_field(
				'neohp_alert_j',
				_('Ctrl+Shift+J (コンソール)'),
				function() {
					$value = esc_html(get_option('neohp_alert_j', '2'));
					echo $this->getselect("neohp_alert_j", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+U
			register_setting('neohp_settings_group', 'neohp_alert_u');
			add_settings_field(
				'neohp_alert_u',
				_('Ctrl+U (HTMLソース表示)'),
				function() {
					$value = esc_html(get_option('neohp_alert_u', '2'));
					echo $this->getselect("neohp_alert_u", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// Ctrl+P
			register_setting('neohp_settings_group', 'neohp_alert_p');
			add_settings_field(
				'neohp_alert_p',
				_('Ctrl+P (印刷)'),
				function() {
					$value = esc_html(get_option('neohp_alert_p', '1'));
					echo $this->getselect("neohp_alert_p", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
					echo _('印刷阻止をするもものの、ブラウザによってはうまく動作しません');
				},
				'neohp-settings',
				'neohp_section'
			);

			// Right Click
			register_setting('neohp_settings_group', 'neohp_alert_r');
			add_settings_field(
				'neohp_alert_r',
				_('右クリック'),
				function() {
					$value = esc_html(get_option('neohp_alert_r', '2'));
					echo $this->getselect("neohp_alert_r", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
						, '2=' . _('妨害＋記録＋表示＋リダイレクト')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// selection
			register_setting('neohp_settings_group', 'neohp_alert_s');
			add_settings_field(
				'neohp_alert_s',
				_('テキスト選択'),
				function() {
					$value = esc_html(get_option('neohp_alert_s', '1'));
					echo $this->getselect("neohp_alert_s", $value
						, '0=' . _('無効')
						, '1=' . _('妨害＋記録のみ')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// debugger
			register_setting('neohp_settings_group', 'neohp_alert_d');
			add_settings_field(
				'neohp_alert_d',
				_('デバッガー妨害'),
				function() {
					$value = esc_html(get_option('neohp_alert_d', '1'));
					echo $this->getselect("neohp_alert_d", $value
						, '0=' . _('無効')
						, '1=' . _('妨害')
					);
				},
				'neohp-settings',
				'neohp_section'
			);

			// セクションの追加
			add_settings_section(
				'neohp_section',
				'基本設定',
				function() {
					echo 
	  _('右クリックやソースコード表示時に転送する URL を設定します') . '<br><br>'
	. _('警告メッセージにはHTMLは使用できません') . '<br><br>'
	. _('以下の文字が使用できます')
	. '<table><tr><td>\n</td><td>' . _('改行')
	. '</td></tr><tr><td>$IP</td><td>' . _('IPアドレス')
	. '</td></tr><tr><td>$URL</td><td>' . _('URL')
	. '</td></tr><tr><td>$KEY</td><td>' . _('押下されたキー')
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
			<h1><?php echo _('Neo HTML Protector設定') ?></h1>
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

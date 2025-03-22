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
					$value = get_option('neohp_htmlprotect_message', $neohp_viewsource_default );
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

			// HTML圧縮
			register_setting('neohp_basic_group', 'neohp_htmlcompress');
			add_settings_field(
				'neohp_htmlcompress',
				__('HTML難読化 (圧縮)', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_htmlcompress', '1'));
					echo $this->getselect("neohp_htmlcompress", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_htmlprotect", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					);

					echo '<br>' .esc_html( __('有効化した時は必ずリダイレクトが発生するため、SEOが落ちるかもしれません', 'neo-html-protector') );
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
					echo $this->getselect("neohp_alert_f12", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_i", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_j", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_u", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_p", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
					echo '<br>' . esc_html( __('印刷阻止をするもものの、ブラウザによってはうまく動作しません', 'neo-html-protector') );
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
					echo $this->getselect("neohp_alert_r", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_c", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
						, '2=' . __('妨害＋記録＋表示＋リダイレクト', 'neo-html-protector')
					);
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// selection
			register_setting('neohp_basic_group', 'neohp_alert_s');
			add_settings_field(
				'neohp_alert_s',
				__('テキスト選択', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('neohp_alert_s', '1'));
					echo $this->getselect("neohp_alert_s", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('妨害＋記録のみ', 'neo-html-protector')
					);
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
					echo $this->getselect("neohp_alert_d", $value
						, '0=' . __('無効', 'neo-html-protector')
						, '1=' . __('有効', 'neo-html-protector')
					);
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
				},
				'neohp-settings',
				'neohp_basic_section'
			);

			// HTML保護時のheadの出力
			register_setting('neohp_advanced_group', 'html_protect_head');
			add_settings_field(
				'html_protect_head',
				__('HTML保護時のHEADタグの出力', 'neo-html-protector'),
				function() {
					$value = esc_html(get_option('html_protect_head', '2'));
					echo $this->getselect("html_protect_head", $value
						, '0=' . __('出力しない', 'neo-html-protector')
						, '1=' . __('TITLEタグのみ', 'neo-html-protector')
						, '2=' . __('WordpressのHEADタグより取得', 'neo-html-protector')
					);
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
						array_push($lang_array, "$k=$v");
					}

					echo $this->getselect("neohp_alert_message_lang", $value
						, '0=' . __('Wordpressの言語', 'neo-html-protector')
						, '1=' . __('ブラウザの設定言語', 'neo-html-protector')
						, $lang_array
					);
					echo '<br>' . __('メッセージをカスタム設定されている場合は言語を変更できません', 'neo-html-protector');
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
						array_push($lang_array, "$k=$v");
					}

					echo $this->getselect("neohp_view-source_message_lang", $value
						, '0=' . __('Wordpressの言語', 'neo-html-protector')
						, '1=' . __('ブラウザの設定言語', 'neo-html-protector')
						, $lang_array
					);
					echo '<br>' . __('メッセージをカスタム設定されている場合は言語を変更できません', 'neo-html-protector');
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
			<h1><?php echo __('Neo HTML Protector 設定', 'neo-html-protector') ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="?page=neohp-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>"><?php echo __('基本設定', 'neo-html-protector') ?></a>
				<a href="?page=neohp-settings&tab=message" class="nav-tab <?php echo $active_tab === 'message' ? 'nav-tab-active' : ''; ?>"><?php echo __('メッセージの設定', 'neo-html-protector') ?></a>
				<a href="?page=neohp-settings&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>"><?php echo __('高度な設定', 'neo-html-protector') ?></a>
				<a href="?page=neohp-settings&tab=clear" class="nav-tab <?php echo $active_tab === 'clear' ? 'nav-tab-active' : ''; ?>"><?php echo __('初期設定に戻す', 'neo-html-protector') ?></a>
				<a href="?page=neohp-settings&tab=about" class="nav-tab <?php echo $active_tab === 'about' ? 'nav-tab-active' : ''; ?>"><?php echo __('このプラグインについて', 'neo-html-protector') ?></a>
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
			<p><?php echo __('データをすべてクリアするには、プラグインをアンインストールしてから、インストールしなおして下さい', 'neo-html-protector') ?></p>
		</div>
		<?php
	}

	// このプラグインについてタブ
	private function about_page() {
		?>
<h2><?php echo __('Neo HTML Protectorについて', 'neo-html-protector') ?></h2>
<p><?php echo __('このプラグインはあなたのWordpressから出力されるHTMLや画像等を保護し、コンテンツの不正利用から守ることを目的としています', 'neo-html-protector') ?></p>
<p><?php echo __('開発者: 夜桜　なの', 'neo-html-protector') ?></p>
<p><?php echo __('バージョン', 'neo-html-protector') ?>: <?php echo NEOHP_VERSION ?></p>
<p><?php echo __('サポートページ', 'neo-html-protector') ?>: <a target="_blank" href="https://support.773.moe/neo-html-protector">https://support.773.moe/neo-html-protector</a></p>
<p><a target="_blank" href="https://support.773.moe/donate/"><img src="<?php echo NEOHP_IMG_URL ?>/nano.gif" alt="Donate Button" width="240" style="border-radius: 30px"></a></p>
<p><script type="text/javascript" src="https://embed.nicovideo.jp/watch/sm44565088/script?w=640&h=360"></script><br><a target="_blank" href="https://www.nicovideo.jp/watch/sm44565088">[<?php echo __('ISISちゃん', 'neo-html-protector') ?>]Give Me Merorin 1.6 <?php echo __('Miss. 裏まにら氏歌唱', 'neo-html-protector') ?></a></p>
<h2><?php echo __('支援のお願い', 'neo-html-protector') ?></h2>
<p><?php echo __('Neo HTML Protectorをご利用いただき、ありがとうございます！', 'neo-html-protector') ?></p>
<p><?php echo __('本プラグインの開発と維持には多くの時間と知恵の絞りだしがかかっており、引き続き改善と更新を行っていくための資金を集めるために、もしご支援いただける方がいれば、寄付をお願いできればと思います。', 'neo-html-protector') ?></p>
<h3><?php echo __('寄付について', 'neo-html-protector') ?></h3>
<p><?php echo __('寄付は任意であり、強制ではありません。プラグインを無料でご利用いただけるように、どなたでも利用可能です。しかし、開発を継続するためにはご支援が大変ありがたく、少しでもご協力いただけると嬉しいです。', 'neo-html-protector') ?></p>

<p><?php echo __('ご寄付は、下記のリンクから行っていただけます。日本の方であれば無料で寄付をすることも可能で、更にはアマゾンギフト券で気軽に寄付をすることが可能です。', 'neo-html-protector') ?></p>
<p><?php echo __('皆様のご支援があれば、今後も素晴らしいアップデートをお届けできるよう頑張ります！', 'neo-html-protector') ?></p>
<p><?php echo __('寄付先', 'neo-html-protector') ?>:<a target="_blank" href="https://support.773.moe/donate">https://support.773.moe/donate</a></p>
<p><?php echo __('ご支援いただけることに感謝し、今後ともよろしくお願いいたします。', 'neo-html-protector') ?></p>
		<?php
	}
}


<?php
/**
 * Neo HTML Protector admin IPログリーダー
 */

if (is_admin()) {
	$neohp_iplogreader = new neohp_iplogreader();
}

class neohp_iplogreader {
	protected $neohp_database;
	protected $neohp_htmlprotect;

	public function __construct() {
		// dbを呼び出し
		$this->neohp_database=new neohp_database();
		$this->neohp_htmlprotect = new neohp_htmlprotect();

		// IPリーダーのメニュー追加
		add_action('admin_menu', array( $this, 'ip_log_reader_menu' ) );

		// IPアドレスを保存するテーブルがなければ作成

		$wpdb=$this->neohp_database->create_user_ip();
	}

	// IPログリーダーの設置
	public function ip_log_reader_menu() {
		add_menu_page(
			__('IPログリーダー', 'neo-html-protector'),	// ページタイトル
			__('IPログリーダー', 'neo-html-protector'),	// メニュータイトル
			'manage_options',		// 権限
			'ip-log-reader',		// メニューのスラッグ
			array($this, 'ip_log_reader_page'),  // 表示する関数
			'dashicons-networking',	// アイコン
			30						// メニューの位置
		);
	}

	// IPアドレスを表示するページ内容
	function ip_log_reader_page() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';

		// CSS読み込み
		?>
<style>
.tablenav-pages {
	text-align: center;
}

.page-number,
.prev-page,
.next-page {
	display: inline-block;
	padding: 8px 14px;
	margin: 2px;
	border: 1px solid #ddd;
	border-radius: 4px;
	background-color: #f9f9f9;
	color: #333;
	text-decoration: none;
	font-size: 14px;
	transition: background-color 0.2s ease;
}

.page-number:hover,
.prev-page:hover,
.next-page:hover {
	background-color: #e0e0e0;
}

.current-page {
	color: #000;
	font-weight: bold;
	background-color: #c0c0ff;
	font-size: 16px;
}

.page-number.active {
	background-color: #0073aa;
	color: #fff;
	border-color: #0073aa;
	font-weight: bold;
}
</style>
		<?php

		// 現在のページを取得（デフォルトは1）
		$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
		$per_page = 100;
		$offset = ($paged - 1) * $per_page;

		// 合計データ数を取得
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
		
		// データを取得
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d OFFSET %d",
			$per_page,
			$offset
		));

		// HTML
		?>
		<div class="wrap">
		<h2><?php echo esc_html( __('IPアドレスログ', 'neo-html-protector') ) ?></h2>
		<form method="post">
		<input type="submit" name="clear_logs" class="button button-primary" value="<?php echo esc_html( __('全クリア', 'neo-html-protector') ) ?>" />
		</form>
		<?php

		// 「全クリア」ボタンが押された場合にログを削除
		if ( isset($_POST['clear_logs']) ) {
			$this->clear_ip_logs(); // ログを削除する関数を呼び出す
			echo '<div class="updated"><p>' . esc_html( __('IPログが全て削除されました', 'neo-html-protector') ) . '</p></div>';
		} else if($results) {
		// ログ表示部分
			?>
			<table class="wp-list-table widefat fixed striped">
			<thead><tr>
			<th><?php echo esc_html( __('ID', 'neo-html-protector') ) ?></th>
			<th colspan='2'><?php echo esc_html( __('IPアドレス', 'neo-html-protector') ) ?></th>
			<th colspan='5'><?php echo esc_html( __('ユーザーエイジェント', 'neo-html-protector') ) ?></th>
			<th colspan='2'><?php echo esc_html( __('イベント', 'neo-html-protector') ) ?></th>
			<th colspan='3'><?php echo esc_html( __('URL', 'neo-html-protector') ) ?></th>
			<th colspan='2'><?php echo esc_html( __('日時', 'neo-html-protector') ) ?></th></tr></thead>
			<tbody>
			<?php

			foreach ($results as $row) {
				?>
				<tr>
					<td><?php echo esc_html( $row->id ) ?></td>
					<td colspan='2'><?php echo esc_html( $row->ip ) ?></td>
					<td colspan='5'><?php echo esc_html( $row->ua ) ?></td>
					<td colspan='2'><?php echo esc_html( $row->keyb ) ?></td>
					<td colspan='3'><?php echo esc_html( $row->url ) ?></td>
					<td colspan='2'><?php echo esc_html( $row->timestamp ) ?></td>
				</tr>
				<?php
			}
			echo '</tbody>';
			echo '</table>';

			// ページングリンクの生成
			$nonce = wp_create_nonce('neohp');
			$total_pages = ceil($total_items / $per_page);
			$base_url = admin_url('admin.php?page=ip-log-reader');

			if ($total_pages > 1) {
				echo '<div class="tablenav">';
				echo '<div class="tablenav-pages">';
				if ($paged > 1) {
					echo '<a class="prev-page button" href="' . esc_url(add_query_arg(array('paged'=>$paged - 1, 'nonce'=>$nonce), $base_url)) . '">«</a>';
				}
				for ($i = 1; $i <= $total_pages; $i++) {
					$active = ($i == $paged) ? ' current-page' : '';
					echo '<a class="page-number' . esc_html($active) . '" href="' . esc_html(add_query_arg(array('paged'=>$i, 'nonce'=>$nonce), $base_url)) . '">' . esc_html($i) . '</a> ';
				}
				if ($paged < $total_pages) {
					echo '<a class="next-page button" href="' . esc_html(add_query_arg(array('paged'=>$paged + 1, 'nonce'=>$nonce), $base_url)) . '">»</a>';
				}
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		} else {
			echo '<p>' . esc_html( __('IPアドレスのデータはありません', 'neo-html-protector') ) . '</p>';
		}
	}

	// IPアドレスのログをクリアする
	protected function clear_ip_logs() {
		global $wpdb;
		// IPログを保存しているテーブル（適切なテーブル名に置き換えてください）
		$table_name = $wpdb->prefix . 'user_ip_log';

		// テーブルの全データを削除
		$wpdb->query("DELETE FROM $table_name");
	}

}

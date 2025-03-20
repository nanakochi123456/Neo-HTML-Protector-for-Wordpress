<?php
/**
 * Neo HTML Protector admin IPログリーダー
 */

$neohp_iplogreader = new neohp_iplogreader();
class neohp_iplogreader {
	public function __construct() {
		// IPリーダーのメニュー追加
		add_action('admin_menu', array( $this, 'ip_log_reader_menu' ) );

		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';

		// IPアドレスを保存するテーブルがなければ作成
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT NOT NULL AUTO_INCREMENT,
				ip VARCHAR(128) NOT NULL,
				url VARCHAR(1024) NOT NULL,
				keyb VARCHAR(128) NOT NULL,
				ua VARCHAR(4096) NOT NULL,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			)"
		);
	}

	// IPログリーダーの設置
	public function ip_log_reader_menu() {
		add_menu_page(
			__('IPログリーダー', NEOHP_DOMAIN),	// ページタイトル
			__('IPログリーダー', NEOHP_DOMAIN),	// メニュータイトル
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
		echo <<<EOM
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
EOM;

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
		<h2><?php echo __('IPアドレスログ', NEOHP_DOMAIN) ?></h2>
		<form method="post">
		<input type="submit" name="clear_logs" class="button button-primary" value="<?php echo __('全クリア', NEOHP_DOMAIN) ?>" />
		</form>
		<?php

		// 「全クリア」ボタンが押された場合にログを削除
		if ( isset($_POST['clear_logs']) ) {
			$this->clear_ip_logs(); // ログを削除する関数を呼び出す
			echo '<div class="updated"><p>' . __('IPログが全て削除されました', NEOHP_DOMAIN) . '</p></div>';
		} else if($results) {
		// ログ表示部分
			?>
			<table class="wp-list-table widefat fixed striped">
			<thead><tr>
			<th><?php echo __('ID', NEOHP_DOMAIN) ?></th>
			<th colspan='2'><?php echo __('IPアドレス', NEOHP_DOMAIN) ?></th>
			<th colspan='5'><?php echo __('ユーザーエイジェント', NEOHP_DOMAIN) ?></th>
			<th colspan='2'><?php echo __('イベント', NEOHP_DOMAIN) ?></th>
			<th colspan='3'><?php echo __('URL', NEOHP_DOMAIN) ?></th>
			<th colspan='2'><?php echo __('日時', NEOHP_DOMAIN) ?></th></tr></thead>
			<tbody>
			<?php

			foreach ($results as $row) {
				echo "<tr>
					<td>{$row->id}</td>
					<td colspan='2'>{$row->ip}</td>
					<td colspan='5'>{$row->ua}</td>
					<td colspan='2'>{$row->keyb}</td>
					<td colspan='3'>{$row->url}</td>
					<td colspan='2'>{$row->timestamp}</td>
				</tr>";
			}
			echo '</tbody>';
			echo '</table>';

			// ページングリンクの生成
			$total_pages = ceil($total_items / $per_page);
			$base_url = admin_url('admin.php?page=ip-log-reader');

			if ($total_pages > 1) {
				echo '<div class="tablenav">';
				echo '<div class="tablenav-pages">';
				if ($paged > 1) {
					echo '<a class="prev-page button" href="' . esc_url(add_query_arg('paged', $paged - 1, $base_url)) . '">«</a>';
				}
				for ($i = 1; $i <= $total_pages; $i++) {
					$active = ($i == $paged) ? ' current-page' : '';
					echo '<a class="page-number' . $active . '" href="' . esc_url(add_query_arg('paged', $i, $base_url)) . '">' . $i . '</a> ';
				}
				if ($paged < $total_pages) {
					echo '<a class="next-page button" href="' . esc_url(add_query_arg('paged', $paged + 1, $base_url)) . '">»</a>';
				}
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		} else {
			echo '<p>' . __('IPアドレスのデータはありません', NEOHP_DOMAIN) . '</p>';
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

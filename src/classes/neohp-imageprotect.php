<?php
/**
 * Neo HTML Protector neohp_imageprotect
 */

define( 'NEOHP_REQUIRED_CRYPTO_JS_VERSION', '4.2.0' );
define('NEOHP_IMAGE_EXPIRE', 60 * 10);
$neohp_imageprotect=new neohp_imageprotect();

class neohp_imageprotect {
	protected $neohp_func;
	protected $neohp_javascript;
	protected $mingif = 'data:image/gif;base64,R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7';
	protected $nonce;

	
	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_javascript = new neohp_javascript();
		$this->nonce = $this->generateNonce();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
				$this->neohp_func->err403();
			}
		}

		session_start();

		// JavaScript挿入
		add_action('wp_enqueue_scripts', array($this->neohp_javascript, 'neohp_script') );

		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）

		if(get_option('neohp_imageprotect', '0') !== '0' ) {
			// headタグをキャプチャ開始

			add_action('template_redirect', function () {
				$this->protectimage();
			}, 0);

			// 画像のリプレイスフィルタ
//			if( ! $this->neohp_func->user() ) {
				if(get_option('neohp_imageprotect', '0') === '1') {
					add_filter('wp_get_attachment_image_attributes', array($this, 'processImageTags'), 100, 2);
				} else {
					add_filter('wp_get_attachment_image_attributes', array($this, 'processImageTagsMode2'), 2, 2);
				}
//			}
		}
	}

	// Mode2で画像を表示する
	function protectimage() {
		if (isset($_GET['neohp']) && $_GET['neohp'] == 'img' && isset($_GET['img'])) {
			$encoded_image_url = sanitize_text_field($_GET['img']);

			if(isset($encoded_image_url)) {
				$transient=$this->neohp_func->gettransient($encoded_image_url);
				$nonce=$this->neohp_func->gettransient($transient . '_nonce');
				$parts = explode(',', $transient);
				
				foreach ($parts as &$part) {
					// 各パーツごとに置換
					$this->neohp_func->deletetransient($part);
				}
				// nonceは放置する
				// $this->neohp_func->deletetransient('nonce');

				$image_url = $this->decodeAndDecryptImageUrl($encoded_image_url, $nonce);
				// WordPressのアップロードディレクトリのURLを取得
				$upload_base_url = content_url();

				// URLからアップロードディレクトリのベース部分を取り除く
				$relative_path = str_replace($upload_base_url, '', $image_url);

				// 先頭のスラッシュを取り除く
				$relative_path = ltrim($relative_path, '/');

				// WordPressのルートパスに追加してパスを生成
				$image_path = ABSPATH . $relative_path;

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

					// 画像を出力して転送
					global $wp_filesystem;

					if ( ! function_exists( 'WP_Filesystem' ) ) {
						require_once ABSPATH . 'wp-admin/includes/file.php';
					}

					WP_Filesystem();

					if ( $wp_filesystem->exists( $image_path ) ) {
						$mime_type = wp_check_filetype( $image_path )['type'];
						if ( $mime_type ) {
							header( 'Content-Type: ' . $mime_type );
							header( 'Content-Length: ' . $wp_filesystem->size( $image_path ) );
							header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
							header( 'Cache-Control: post-check=0, pre-check=0', false);
							header( 'Pragma: no-cache' );
							header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
							// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
							// This is image file (binary)

							echo $wp_filesystem->get_contents( $image_path );
							// phpcs:enable
							exit;
						}
					}
				}
				wp_die(esc_html(__('画像が見つかりません', 'neo-html-protector')));
			}
		}
	}
	
	// 画像URLをnonceを使って暗号化し、Base64エンコードする関数
	function encryptAndEncodeImageUrl($url, $nonce) {
		// 暗号化方法（AES-256-CBC）
		$method = 'AES-256-CBC';

		// nonceをパスワードとして使用し、暗号化キーを生成
		$key = hash('sha256', $nonce, true); // sha256で生成された256ビットキー
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); // 初期化ベクトルの生成

		// URLを暗号化
		$encrypted_url = openssl_encrypt($url, $method, $key, 0, $iv);

		// IVもBase64でエンコードしてURLに含める
		$encoded_iv = base64_encode($iv);

		// 暗号化されたURLとIVを一緒に返す（Base64エンコードされたURL）
		return urlencode(
			$this->swap_Case(
				$this->neohp_func->url_safe_base64_encode($encrypted_url . ':' . $encoded_iv)
			)
		);
	}

	function decodeAndDecryptImageUrl($encodedData, $nonce) {
		// 暗号化方法（AES-256-CBC）
		$method = 'AES-256-CBC';
		
		// nonceをパスワードとして使用し、暗号化キーを生成
		$key = hash('sha256', $nonce, true); // sha256で生成された256ビットキー

		// URLデコードしてケースを入れ替え
		$decodedData = $this->neohp_func->url_safe_base64_decode(
			$this->swap_case(
				urldecode($encodedData)
			)
		);

			// 暗号化されたデータとIVを分割
		// 暗号化されたデータとIVを分割
		$parts = explode(':', $decodedData);
		if (count($parts) !== 2) {
			error_log("Invalid data format: " . $decodedData);
			return "";
		}

		list($encrypted_url, $encoded_iv) = $parts;

		// IVをデコード
		$iv = base64_decode($encoded_iv);

		// 暗号化されたURLを復号
		$decrypted_url = openssl_decrypt($encrypted_url, $method, $key, 0, $iv);

		return $decrypted_url;
	}

	// nonce生成のための関数
	function generateNonce() {
		return bin2hex(random_bytes(32)); // 32バイトのランダムなnonceを生成
	}

	// URLからhttp://example.com:80 を削除して、画像データも保護であれば頭にクエリーとnonceをつける
	function removeSchemeAndHost($url) {
		$pattern = '/https?:\/\/(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,6}(?::\d+)?/i';

		// カンマで分割
		$parts = explode(',', $url);
		
		foreach ($parts as &$part) {
			// 各パーツごとに置換
			$part = preg_replace($pattern, '', $part);
		}
		
		// カンマで再構築
		$url = implode(',', $parts);

		return $url;
	}

	//どうせなら大文字と小文字を入れ替える
	function swap_case($str) {
		return preg_replace_callback('/[a-zA-Z]/', function ($match) {
			$char = $match[0];
			return ctype_lower($char) ? strtoupper($char) : strtolower($char);
		}, $str);
	}
	// 実際の処理 mode1
	function processImageTags($attributes, $attachment) {
		$site_url = get_site_url();

		if (isset($attributes['src'])) {
			// site_urlと同一の時のみ処理する
			if (strpos($attributes['src'], $site_url) === 0) {
				// classを追加する
				if (isset($attributes['class'])) {
					$attributes['class'] .= ' protected';
				} else {
					$attributes['class'] = 'protected';
				}

				// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
				$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $this->nonce);
				$attributes['data-src'] = $encoded_src;
				// 最小GIFをsrcに設定
				$attributes['src'] = $this->mingif;

				if (isset($attributes['srcset'])) {
					$encoded_srcset = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['srcset']), $this->nonce);
					$attributes['data-srcset'] = $encoded_srcset;
					unset($attributes['srcset']); // srcset属性を削除
				}

				// nonceをimgタグに追加（オプション）
				$attributes['data-nonce'] = $this->nonce;
			}
		}
		return $attributes;
	}
	// 実際の処理 mode2
	function processImageTagsMode2($attributes, $attachment) {
		$site_url = get_site_url();
		// transientの初期化
		$transient = [];

		if (isset($attributes['src'])) {
			// site_urlと同一の時のみ処理する
			if (strpos($attributes['src'], $site_url) === 0) {
				// classを追加する
				if (isset($attributes['class'])) {
					$attributes['class'] .= ' protected';
				} else {
					$attributes['class'] = 'protected';
				}
				// classを追加する
				if (isset($attributes['class'])) {
					$attributes['class'] .= ' protected';
				} else {
					$attributes['class'] = 'protected';
				}

				// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
				$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $this->nonce);
				$attributes['src'] =
					$site_url . '?neohp=img&img='
							  .  $encoded_src;
				// エンコードしたsrcを名前にしてtransientセット
				array_push($transient, $encoded_src);

				if (isset($attributes['srcset'])) {
					$parts = explode(',', $attributes['srcset'] );
					foreach ($parts as &$part) {
						// 各パーツごとに置換
						$pattern = '/([^\s,]+)\s*(\d+)(x|w|h)?/';
						preg_match($pattern, $part, $matches);
						if (!empty($matches)) {
							$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($matches[1]), $this->nonce);	// URL
	//$encoded_src=$this->removeSchemeAndHost($matches[1]);
							$size = $matches[2]; 		//100
							$type = isset($matches[3]) ? $matches[3] : '';	// x or w or h
							$part = 
								$site_url . '?neohp=img&img='
										  .  $encoded_src
										  . ' ' . $size . $type;
							// エンコードしたsrcを名前にしてtransientセット
							array_push($transient, $encoded_src);
						}
					}
					$url = implode(',', $parts);
					$attributes['srcset'] = $url;
				}

				// 結合しすべてにpushする
				$transientstr = implode(',', $transient);
				foreach ($transient as $transitent_name) {
					$this->neohp_func->settransient(
						$transitent_name, $transientstr, NEOHP_IMAGE_EXPIRE);
				}

				// nonceをcookieに追加（オプション）
				$this->neohp_func->settransient(
					$transientstr . '_nonce', $this->nonce, NEOHP_IMAGE_EXPIRE);

				$attributes['data-nonce'] = $this->nonce;
			}
		}
		return $attributes;
	}
}

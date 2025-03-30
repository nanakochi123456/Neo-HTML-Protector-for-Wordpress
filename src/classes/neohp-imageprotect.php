<?php
/**
 * Neo HTML Protector neohp_imageprotect
 */

$neohp_imageprotect=new neohp_imageprotect();

class neohp_imageprotect {
	protected $neohp_func;
	protected $neohp_javascript;
	protected $mingif = 'data:image/gif;base64,R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7';
	protected $outputPngDone = false;
	protected $neohp_hash_keys  = "sha256";
	protected $neohp_hash_keys_js = "sha256";
	protected $neohp_nonce_bits;
	protected $neohp_image_expire;

	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_javascript = new neohp_javascript();

		add_action( 'plugins_loaded', function() {
			if(get_option('neohp_deny_imagebot', '0') === '1') {
				if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
					$this->neohp_func->err403();
				}
			}

			// JavaScript挿入
			add_action('wp_enqueue_scripts', array($this->neohp_javascript, 'neohp_script') );

			// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）

			if(get_option('neohp_imageprotect', '0') !== '0' && ! $this->neohp_func->user() ) {
				// https://milltalk.jp/boards/212564 より20分が妥当
				$this->neohp_image_expire = (int)get_option('neohp_nonce_expire', '20') * 60;

				// 暗号化強度の設定
				$this->neohp_hash_keys = get_option('neohp_hash_bits', 'sha256');
				$this->neohp_hash_keys_js = get_option('neohp_hashjs_bits', 'sha256');
				$this->neohp_nonce_bits = get_option('neohp_nonce_bits', '32');
				$this->neohp_nonce_bits=(int)$this->neohp_nonce_bits;
				if($this->neohp_nonce_bits < 16) {
					$this->neohp_nonce_bits = 16;
				}


				// headタグをキャプチャ開始

				add_action('template_redirect', function () {
					$this->protectimage();
				}, 0);

				// 画像のリプレイスフィルタ
				if(get_option('neohp_imageprotect', '0') === '1' ) {
					add_filter('wp_get_attachment_image_attributes', array($this, 'processImageTagsMode1'), 2, 2);
					if(get_option('neohp_imageprotect', '0') !== '0' ) {
						add_filter('wp_get_attachment_image_attributes', array($this, 'processImageTagsMode1js'), 2, 2);
					}
				}

				// 全体のHTMLを書き換える
				if(get_option('neohp_imageprotect', '0') === '2' ) {
					add_action('wp_head', function () {
						ob_start();
					}, 0);

					// これでHTMLすべてキャプチャできるはず
					add_action('wp_footer', function () {
						$neohp_all_content = ob_get_clean(); // head内容を取得
						ob_end_clean();
						$lang = get_bloginfo('language');
						$html = '<!doctype html><html lang="' . $lang . '"><head><meta charset="UTF-8">';
						echo $html;
						if( ! $this->neohp_func->user() ) {
							$content=$this->processImageTagsMode2($neohp_all_content);
							if(get_option('neohp_imageprotectjs', '0') !== '0' ) {
								$content=$this->processImageTagsMode2js($content);
							}
							echo $content;
						} else {
							echo $neohp_all_content;
						}
					}, 99999);
				}
			}
		});
	}

	// 文字列を1文字ずつ処理して改行を挿入する関数
	function wrapText($text, $fontPath, $fontSize, $maxWidth) {
		$lines = [];
		$currentLine = '';

		// 改行を考慮して行に分割
		$parts = preg_split('/\n/', $text);

		foreach ($parts as $part) {
			// 1文字ずつ処理
			for ($i = 0; $i < strlen($part); $i++) {
				$char = $part[$i];
				$bbox = imagettfbbox($fontSize, 0, $fontPath, $currentLine . $char);
				$lineWidth = $bbox[2] - $bbox[0]; // 幅を正しく計算

				// 文字を追加して幅が最大幅を超えるかチェック
				if ($lineWidth <= $maxWidth) {
					$currentLine .= $char;
				} else {
					if (strlen($currentLine) > 0) {
						$lines[] = $currentLine; // 現在の行を保存
					}
					$currentLine = $char; // 新しい行を作成
				}
			}

			// 行の終わりで確定して次の行に移動
//			if (strlen($currentLine) > 0) {
				$lines[] = $currentLine;
				$currentLine = ''; // 行をリセット
//			}
		}

		return $lines;
	}

	// pngで文字列を描画し、終了する
	function outputPng($text) {
		static $neohp_outputPngDone = false;
		// 画像の幅と高さ
		$width = 1200;
		$height = 800;

		// 画像作成
		$image = imagecreatetruecolor($width, $height);

		// 背景色設定（黄色）
		$bgColor = imagecolorallocate($image, 255, 255, 0);
		imagefill($image, 0, 0, $bgColor);

		// 文字色設定（黒色）
		$textColor = imagecolorallocate($image, 0, 0, 0);

		// フォントファイルのパス
		$fontPath = __DIR__ . '/VeraSe.ttf'; // フォントファイルの場所（スクリプトと同じディレクトリ）

		// 文字列
		//$text = "Hello, World! This is a long text that should wrap.";

		// フォントサイズ
		$fontSize = 16;

		// 行の最大幅を設定
		$maxWidth = $width - 20; // 左右に少し余白を残す

		// テキストを改行付きで分割
		$lines = $this->wrapText($text, $fontPath, $fontSize, $maxWidth);

		// 初期のY座標を設定
		$y = 40; // Y座標（適宜調整）

		// 各行を描画
		foreach ($lines as $line) {
			// 文字列のバウンディングボックスを取得
			$bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
			// 文字列の幅を計算
			$textWidth = $bbox[2] - $bbox[0];
			// X座標を中央に設定
			$x = (int)(($width - $textWidth) / 2);

			// 文字を描画
			imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $line);
			// 次の行のY座標を設定
			$y += $fontSize + 15; // 行間を調整
		}

		// ヘッダーを送信して画像を出力
		header('Content-Type: image/png');
		$this->neohp_func->cachezero();
		imagepng($image);

		// 画像リソースを解放
		imagedestroy($image);
		exit;
	}

	// 警告出力関数＆デバッグ
	function alert($string, $image_path, $mime, $nonce, $transient, $encoded_image_url) {
		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

		$value = get_option('neohp_imagedownload_message', $neohp_imagedownload_default );
		$user_ip = $this->neohp_func->get_user_ip();
		$ua = $this->neohp_func->get_user_agent();

		$value = str_replace('$IP', $user_ip, $value);
		$value = str_replace('$UA', $ua, $value);
		$value = str_replace('\\n', "\n", $value);

		if(get_option('neohp_imagedownload_real', '0') === '0') {
			$this->outputPng($value . "\n"
				. "string=".$string . "\n"
				. "image_path=".$image_path . "\n"
				. "nonce=".$nonce . "\n"
			);
		} elseif(get_option('neohp_imagedownload_real', '0') === '1') {
			header('Content-Type: image/gif');
			$mingif_array=explode(',', $this->mingif);
			$this->neohp_func->cachezero();
			echo base64_decode($mingif_array[1]);
		} elseif(get_option('neohp_imagedownload_real', '0') === '2') {
			header('Content-Type: text/html');
			$this->neohp_func->cachezero();
			echo '<!doctype html><html></html>';
		}
		exit;
	}

	// Mode1で画像を表示する
	function protectimage() {
		if (isset($_GET['neohp']) && $_GET['neohp'] == 'img' && isset($_GET['img'])) {
			$encoded_image_url = sanitize_text_field($_GET['img']);

			if(isset($encoded_image_url)) {
				$transient=$this->neohp_func->gettransient($encoded_image_url);
				if($transient !== '') {
					$nonce=$this->neohp_func->gettransient($encoded_image_url . '_nonce');
					$parts = explode(',', $transient);
					
					foreach ($parts as &$part) {
						// 各パーツごとに置換
						$this->neohp_func->deletetransient($part);
						$this->neohp_func->deletetransient($part . '_nonce');
					}
					$image_url = $this->decodeAndDecryptImageUrl($encoded_image_url, $nonce, $this->neohp_hash_keys);
				} else {
					$image_url = '';
				}

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
/*
				$this->alert(
					  'outputed'
					, $image_path
					, $mime
					, $nonce
					, ""//$transient
					, $encoded_image_url
				);
*/
					if (!in_array($mime, $allowed_types)) {
					} else {
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
								$this->neohp_func->cachezero();
								// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
								// This is image file (binary)

								echo $wp_filesystem->get_contents( $image_path );
								// phpcs:enable
								exit;
							}
						}
					}
				}
				$this->alert(
					  'outputed'
					, $image_path
					, $mime
					, $nonce
					, ""
					, $transient
				);
			}
			$this->alert(
				  'function end'
				, $image_path
				, $mime
				, $nonce
			);
		}
	}
	
	// 画像URLをnonceを使って暗号化し、Base64エンコードする関数
	function encryptAndEncodeImageUrl($url, $nonce, $sha) {
		// 暗号化方法（AES-256-CBC）
		$method = 'AES-256-CBC';
		// nonceをパスワードとして使用し、暗号化キーを生成
		$key = hash($sha, $nonce, true); // sha512?で生成された512ビットキー
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); // 初期化ベクトルの生成

		// URLを暗号化
		$encrypted_url = openssl_encrypt($url, $method, $key, 0, $iv);

		// IVもBase64でエンコードしてURLに含める
		$encoded_iv = base64_encode($iv);

		// 暗号化されたURLとIVを一緒に返す（Base64エンコードされたURL）
		return urlencode(
			$this->swap_case(
				$this->neohp_func->url_safe_base64_encode($encrypted_url . ':' . $encoded_iv)
			)
		);
	}

	function decodeAndDecryptImageUrl($encodedData, $nonce, $sha) {
		// 暗号化方法（AES-256-CBC）
		$method = 'AES-256-CBC';

		// nonceをパスワードとして使用し、暗号化キーを生成
		$key = hash($sha, $nonce, true);

		// URLデコードしてケースを入れ替え
		$decodedData = $this->neohp_func->url_safe_base64_decode(
			$this->swap_case(
				urldecode($encodedData)
			)
		);

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
		return $this->swap_case(
			$this->neohp_func->url_safe_base64_encode(
				random_bytes($this->neohp_nonce_bits)
			)
		);
	}

	// URLからhttp://example.com:80 を削除
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

	// どうせなら大文字と小文字を入れ替える
	function swap_case($str) {
		return preg_replace_callback('/[a-zA-Z]/', function ($match) {
			$char = $match[0];
			return ctype_lower($char) ? strtoupper($char) : strtolower($char);
		}, $str);
	}

	// 実際の処理 mode2
	function processImageTagsMode2($html) {
		// セキュリティ制御のためのnonce生成
		$nonce = $this->generateNonce();

		// imgタグを正規表現で処理
		$html = preg_replace_callback('/<img[^>]+>/i', function($matches) use ($nonce) {
			$img_tag = $matches[0];
			$site_url = get_site_url();

			// imgタグの属性をパース
			$attributes = [];
			preg_match_all('/([a-zA-Z0-9\-]+)="([^"]+)"/', $img_tag, $attr_matches, PREG_SET_ORDER);
			
			foreach ($attr_matches as $attr) {
				$attributes[$attr[1]] = $attr[2];
			}

			// transientの初期化
			$transient = [];

			if (isset($attributes['src'])) {
				// site_urlと同一の時のみ処理する
				if (strpos($attributes['src'], $site_url) === 0) {
					// classを追加する
					if (isset($attributes['class'])) {
						if (strpos($attributes['class'], 'protected') === false) {
							$attributes['class'] .= ' protected';
						}
					} else {
						$attributes['class'] = 'protected';
					}
					// classを追加する
					if (isset($attributes['class'])) {
						if (strpos($attributes['class'], 'protected') === false) {
							$attributes['class'] .= ' protected';
						}
					} else {
						$attributes['class'] = 'protected';
					}

					// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
					$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $nonce, $this->neohp_hash_keys);
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
								$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($matches[1]), $nonce, $this->neohp_hash_keys);	// URL
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
							$transitent_name, $transientstr, $this->neohp_image_expire);
						$this->neohp_func->settransient(
							$transitent_name . '_nonce', $nonce, $this->neohp_image_expire);
					}

					// 新しいimgタグを作成
					$new_img_tag = '<img';
					foreach ($attributes as $attr_name => $attr_value) {
						$new_img_tag .= ' ' . $attr_name . '="' . $attr_value . '"';
					}
					$new_img_tag .= '>';

					return $new_img_tag;
				} else {
					return $img_tag;
				}
			}
		}, $html);

		return $html;
	}


	// 実際の処理 Mode1
	function processImageTagsMode1($attributes, $attachment) {
		$site_url = get_site_url();
		// transientの初期化
		$transient = [];
		$nonce=$this->generateNonce();
		if (isset($attributes['src'])) {
			// site_urlと同一の時のみ処理する
			if (strpos($attributes['src'], $site_url) === 0) {
				// classを追加する
				if (isset($attributes['class'])) {
					if (strpos($attributes['class'], 'protected') === false) {
						$attributes['class'] .= ' protected';
					}
				} else {
					$attributes['class'] = 'protected';
				}
				// classを追加する
				if (isset($attributes['class'])) {
					if (strpos($attributes['class'], 'protected') === false) {
						$attributes['class'] .= ' protected';
					}
				} else {
					$attributes['class'] = 'protected';
				}

				// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
				$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $nonce, $this->neohp_hash_keys);
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
							$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($matches[1]), $nonce, $this->neohp_hash_keys);	// URL
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
						$transitent_name, $transientstr, $this->neohp_image_expire);
					$this->neohp_func->settransient(
						$transitent_name . '_nonce', $nonce, $this->neohp_image_expire);
				}
			}
		}
		return $attributes;
	}

	function processImageTagsMode1js($attributes, $attachment) {
		// セキュリティ制御のためのnonce生成
		$nonce = $this->generateNonce();

		$site_url = get_site_url();

		// imgタグの属性をパース
//		$attributes = [];
//		preg_match_all('/([a-zA-Z0-9\-]+)="([^"]+)"/', $img_tag, $attr_matches, PREG_SET_ORDER);
		
//		foreach ($attr_matches as $attr) {
//			$attributes[$attr[1]] = $attr[2];
//		}

		if (isset($attributes['src'])) {
			// site_urlと同一の時のみ処理する
			if (strpos($attributes['src'], $site_url) === 0) {
				// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
				$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $nonce, 'sha256');
				$attributes['data-src'] = $encoded_src;
				// 最小GIFをsrcに設定
				$attributes['src'] = $this->mingif;

				if (isset($attributes['srcset'])) {
					$encoded_srcset = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['srcset']), $nonce, 'sha256');
					$attributes['data-srcset'] = $encoded_srcset;
					unset($attributes['srcset']); // srcset属性を削除
				}

				// nonceをimgタグに追加（オプション）
				$attributes['data-nonce'] = $nonce;
			}
		}
		return $attributes;
	}

	function processImageTagsMode2js($html) {
		// セキュリティ制御のためのnonce生成
		$nonce = $this->generateNonce();

		// imgタグを正規表現で処理
		$html = preg_replace_callback('/<img[^>]+>/i', function($matches) use ($nonce) {	
			$img_tag = $matches[0];
			$site_url = get_site_url();

			// imgタグの属性をパース
			$attributes = [];
			preg_match_all('/([a-zA-Z0-9\-]+)="([^"]+)"/', $img_tag, $attr_matches, PREG_SET_ORDER);
			
			foreach ($attr_matches as $attr) {
				$attributes[$attr[1]] = $attr[2];
			}

			if (isset($attributes['src'])) {
				// site_urlと同一の時のみ処理する
				if (strpos($attributes['src'], $site_url) === 0) {
					// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
					$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $nonce, 'sha256');
					$attributes['data-src'] = $encoded_src;
					// 最小GIFをsrcに設定
					$attributes['src'] = $this->mingif;

					if (isset($attributes['srcset'])) {
						$encoded_srcset = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['srcset']), $nonce, 'sha256');
						$attributes['data-srcset'] = $encoded_srcset;
						unset($attributes['srcset']); // srcset属性を削除
					}

					// nonceをimgタグに追加（オプション）
					$attributes['data-nonce'] = $nonce;

					// 新しいimgタグを作成
					$new_img_tag = '<img';
					foreach ($attributes as $attr_name => $attr_value) {
						$new_img_tag .= ' ' . $attr_name . '="' . $attr_value . '"';
					}
					$new_img_tag .= '>';

					return $new_img_tag;
				} else {
					return $img_tag;
				}
			}
		}, $html);

		return $html;
	}
}

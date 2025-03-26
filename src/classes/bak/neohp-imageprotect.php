<?php
/**
 * Neo HTML Protector neohp_imageprotect
 */

$neohp_imageprotect=new neohp_imageprotect();
class neohp_imageprotect {
	protected $neohp_func;
	protected $neohp_jskeyblock;
	protected $neohp_head_content;

	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_jskeyblock = new neohp_jskeyblock();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
				$this->neohp_func->err403();
			}
		}

		// JavaScript挿入
		add_action('wp_enqueue_scripts', array($this->neohp_jskeyblock, 'neohp_script') );

		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）
		if(get_option('neohp_imageprotect', '0') == 1 ) {
			// headタグをキャプチャ開始
			add_action('wp_head', function () {
				ob_start();
			}, 0);

			// これでHTMLすべてキャプチャできるはず
			add_action('wp_footer', function () {
				$neohp_all_content = ob_get_clean(); // head内容を取得
				ob_end_clean();
				if( ! $this->neohp_func->user() ) {
					echo $this->processImageTags($neohp_all_content);
				} else {
					echo $neohp_all_content;
				}
			}, PHP_INT_MAX);
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
		return urlencode(base64_encode($encrypted_url . '::' . $encoded_iv));
	}

	// nonce生成のための関数
	function generateNonce() {
		return bin2hex(random_bytes(16)); // 16バイトのランダムなnonceを生成
	}

	// 実際の処理
	function processImageTags($html) {
		// セキュリティ制御のためのnonce生成
		$nonce = $this->generateNonce();

		// imgタグを正規表現で処理
		$html = preg_replace_callback('/<img[^>]+>/i', function($matches) use ($nonce) {
			$img_tag = $matches[0];

			// imgタグの属性をパース
			$attributes = [];
			preg_match_all('/([a-zA-Z0-9\-]+)="([^"]+)"/', $img_tag, $attr_matches, PREG_SET_ORDER);
			
			foreach ($attr_matches as $attr) {
				$attributes[$attr[1]] = $attr[2];
			}

			// srcとsrcsetが存在する場合、それらを暗号化してBase64エンコードして変換
			if (isset($attributes['src'])) {
				$encoded_src = $this->encryptAndEncodeImageUrl($attributes['src'], $nonce);
				$attributes['data-src'] = $encoded_src;
				// 最小GIFをsrcに設定
				$attributes['src'] = 'data:image/gif;base64,R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7';
			}

			if (isset($attributes['srcset'])) {
				$encoded_srcset = $this->encryptAndEncodeImageUrl($attributes['srcset'], $nonce);
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
		}, $html);

		return $html;
	}
}

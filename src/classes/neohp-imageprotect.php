<?php
/**
 * Neo HTML Protector neohp_imageprotect
 */

$neohp_imageprotect=new neohp_imageprotect();
class neohp_imageprotect {
	protected $neohp_func;
	protected $neohp_javascript;
	protected $mingif = 'data:image/gif;base64,R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7';

	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_javascript = new neohp_javascript();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
				$this->neohp_func->err403();
			}
		}

		// JavaScript挿入
		add_action('wp_enqueue_scripts', array($this->neohp_javascript, 'neohp_script') );

		// 高い優先度でリダイレクト処理を追加（template_redirectフックを使用）

		if(get_option('neohp_imageprotect', '0') !== '0' ) {
			// headタグをキャプチャ開始
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
		return urlencode($this->swap_Case($this->url_safe_base64_encode($encrypted_url . '::' . $encoded_iv)));
	}

	// nonce生成のための関数
	function generateNonce() {
		return bin2hex(random_bytes(16)); // 16バイトのランダムなnonceを生成
	}

function url_safe_base64_encode($data) {
	return str_replace(['+', '/', '='], ['-', '_', '.'], base64_encode($data));
}

	// URLからhttp://example.com:80 を削除して、画像データも保護であれば頭にクエリーとnonceをつける
	function removeSchemeAndHost($url) {
return $url;
		$nonce = wp_create_nonce('neohp_imageprotect');
		$site_url = get_site_url();
		$replace  = '';
		if(0){
		$replace = "$site_url?neohp=protect&nonce=$nonce&img=";
		}

		$pattern = '/https?:\/\/(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,6}(?::\d+)?/i';

		// カンマで分割
		$parts = explode(',', $url);
		
		foreach ($parts as &$part) {
			// 各パーツごとに置換
			$part = preg_replace($pattern, $replace, $part);
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
				$encoded_src = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['src']), $nonce);
				$attributes['data-src'] = $encoded_src;
				// 最小GIFをsrcに設定
				$attributes['src'] = $this->mingif;
			}

			if (isset($attributes['srcset'])) {
				$encoded_srcset = $this->encryptAndEncodeImageUrl($this->removeSchemeAndHost($attributes['srcset']), $nonce);
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

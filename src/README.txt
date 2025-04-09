=== Neo HTML Protector ===
Contributors: Nano Yozakura
Donate link: https://support.773.moe/donate
Tags: Protect Copyright HTML Image Contents
Requires at least: 6.0
Tested up to: 6.7.2
Stable tag: 1.0
Requires PHP: 8.1
License: GPLv2 or later, Partial Reproduction Prohibited
License URI: https://support.773.moe/neo-html-protector-license

ChatGPTにコードを書かせたとても強力なHTML/画像プロテクタ

== Description ==

HTML並びに画像を保護し、操作妨害、ロギングを行い

必要に応じて通常ウェブサイトで操作しないイベントを発火させたら

画面とBGMで精神的威圧をかけるプラグインです

[Documentation](https://support.773.moe/neo-html-protector/)

== Features ==

* ChatGPTを用いて制作されたコード
* nginx+fpm環境、Apache環境のWordpressに対応
* CSSアニメーションやBGM、アスキーアートで精神的威圧

より詳しくは以下のページで説明しています。
[公式サイト](https://support.773.moe/neo-html-protector/)

== Frequently Asked Questions ==

= HTMLを保護するとどれぐらい強力に保護しますか？ =

HTML保護をするとbodyタグの中身が全く見えなくなります

JavaScriptとcookieで毎回リダイレクトを行い、コンテンツを閲覧できるようになります

なお、SEOを狙うコンテンツに使用することはできません

= 画像を保護するとどれぐらい強力に保護しますか？ =

画像はAES-256-CBCで暗号化したワンタイムURLを発行し、そのワンタイムURLにアクセスしたら、無効になります

設定画面でワンタイムURLの有効期限を設定することができます（デフォルトは20分）

また、画像URLを保護すると、JavaScriptにおいてもAES-256-CBCを復号化します

画像URLを保護すると、デバッグモードからでも容易にURLを特定することが困難になります

なお、デフォルトの設定においては、SNSシェアを考慮して、OGPに設定されたアイキャッチの画像は保護されません。設定を変更してください

= キャッシュ系のプラグインと同時に使用することはできますか？ =

できません

なるだけ高速なサーバーで運用することをお勧めします

= HTMLのソース表示を検出することはできますか？ =

HTML保護をした時のみ可能です

ほとんどの検索エンジンに対するbotやSNSのシェアで動作するbotは検出しないようにしてありますが、一部のテキストブラウザやbot等も一緒に検出されます

= 指定したページのみ保護することは可能ですか？ =

現在未対応です

= いずれ復号化することはできるのですよね？ =

はい、そのとおりです

相当頑張れば復号化することはできます

ただし、コピープロテクトをかけたことになりますので、国の法律において保護されることとなります

= 印刷は保護できますか？ =

はい、できます

ただし警告を出すことができない場合があります

= OSのスクリーンショット機能を保護できますか？ =

スクリーンショットキーは単一、もしくは複数のキーを押して取得することができるか、もしくはサードパーティーツールにおいて取得できます

OSのスクリーンショット機能を完全に妨害・警告するのは不可能ですが、可能な限りそのアクションを阻止するよう動作をします

なお、サードパーティーツールについては未対応です

= テキスト選択、コピーを阻止することはできますか？ =

はい、可能です

= HTML等のダウンローダーを阻止することはできますか？ =

一部可能です

コンテンツにつきましてはダウンロードできてしまいますが、画像はブロックされます

= 動作確認したOSは？ =
HTMLプロテクト機能についてはWindows11、Chrome OS最新版、macOS12 Monterey、Linux最新版で確認しています

その他表示確認についてはiOS、Androidでも確認しています

= 日本語には対応してますか？ =

日本語など36言語に対応しています

== Screenshots ==



== Changelog ==

= 1.0 =
* 初回アップロード

これ以前の変更履歴は[サポートページまで](https://support.773.moe/neo-html-protector/)、もしくはプラグインディレクトリ内のREADME.md等で

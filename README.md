# Neo HTML Protector for Wordpress

## 特徴
100% ChatGPT Made Code

ユーザーログインしている時は特に動作しない

何らかの方法で操作出来てしまうが、抑止には良い

## インストール
WPROOT/wp-content/plugins/Neo-HTML-Protector ディレクトリを作成し
その中にsrc配下のファイルをすべて入れて有効化

## 以下のキー操作に対して可能な限り無効化した後、警告を出した上で、ログに残します
- F12
- Ctrl + U
- Ctrl + Shift + I
- Ctrl + Shift + J
- 右クリック
- Ctrl + P (ブラウザによってはうまく動かない、CSSにより妨害する）

## HTMLを可能な限り難読化します
基本的な方式でHTMLを難読化します

エンコード等は行っていません

## キャッシュ系のプラグインを使用されている場合
HTML保護が利用できません

また、このプラグインのアップデート・削除する場合
一度キャッシュを削除してください

## 設定画面
設定→Neo HTML Protector

## IPビュワー
IPV4環境でのみ確認していますが、IPV6でも動作するはずです


## アンインストール
- 無効化して削除

## バージョン履歴
v0.0.23 - ソース表示警告メッセージをWARNINGだけでなく日本語の警告を加えた

v0.0.22 - v0.0.19で実装した機能がFirefox/Safariでうまく動作しないため機能を削除

v0.0.21 - ソース表示するとWARNINGのアスキーアートを表示するようにした（フォント選択可能）

v0.0.20 - headタグの制御を実際に実装

v0.0.19 - HTMLソースの警告を通常のコメントタグだけでなく input type="hidden"、meta name="ALERT" を選択できるようにした

v0.0.18 - HTMLソース表示した時の言語を自動・強制変更できるようにした

v0.0.17 - 半自動翻訳の少しの高品質化のため、メッセージを一部変更、その他deeplで2025/03/23現在対応している言語にすべて対応、プラグインについての中に動画を埋め込み、全クリア機能は当面なくし、その旨のメッセージを記載

v0.0.16 - deepl + dptran による半自動翻訳を実装、イギリス英語、韓国語、中国語2つに対応

v0.0.15 - 設定画面の大幅アップデート、一部関数ファイル化

v0.0.14 - HTML保護時のリダイレクトにおいてに10秒のショートnonceを搭載し、不正アクセスを簡易検知するようにした

v0.0.13 - HTML保護時だけではなく、HTML難読化時にも先頭にコメントメッセージを入れられるようにした

v0.0.12 - bugfix

v0.0.11 - HTML保護時にJavaScript無効時のみCookieとJavaScriptが有効でないと見れないよ、というメッセージを出力するようにした、なおcookieも不可能ではないが更にもう１回リダイレクトが発生するので断念、あわせてOGP画像が本物であるかチェックするようにした

v0.0.10 - view-source: のロギングでテンポラリ→ログに移動時、テンポラリデータベースを削除してなかったのを修正

v0.0.9 - HTML保護時に<script>～</script>を削除しすぎたのを修正、ld+jsonのを残す

v0.0.8 - view-source: の操作をロギングした（ただし操作から10秒かかる）、なおこれにはテキストブラウザでの考慮はされていない、その他データベース関連のクラス化

v0.0.7- Plugih Checkに叱られた所を一部のみ修正、HTMLProtect時に、wp_headより<head>～</head>間を取得するようにした

v0.0.6 - プラグインページに設定画面を追加、admin関係のクラスをisadminで確認して読むようにした

v0.0.5 - UAの保存に対応、IPログビュアーの幅を設定、スマホの選択阻止等に対応

v0.0.1 - 初版 - Neo Copykey Alert と Neo-HTML-View-Protection を結合し、国際化して（英語のみ）クラス化した

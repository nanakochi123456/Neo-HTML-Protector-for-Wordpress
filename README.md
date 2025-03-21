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
v0.0.7- Plugih Checkに叱られた所を一部のみ修正、HTMLProtect時に、wp_headより<head>～</head>間を取得するようにした

v0.0.6 - プラグインページに設定画面を追加、admin関係のクラスをisadminで確認して読むようにした

v0.0.5 - UAの保存に対応、IPログビュアーの幅を設定、スマホの選択阻止等に対応

v0.0.1 - 初版 - Neo Copykey Alert と Neo-HTML-View-Protection を結合し、国際化して（英語のみ）クラス化した

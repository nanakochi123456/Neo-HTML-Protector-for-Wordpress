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
v0.1.6 - lazyloadをnonceの有効期限の30秒前に強制読み込みするようにした、これで長時間Webサイトを放置しても正しく画像が読み込める

v0.1.4 - Ctrl+A、Ctrl+Shift+Cの追加

v0.1.3 - メッセージ追加(Firefoxに関する）、MP3のゲイン調整、楽曲追加

v0.1.2 - ピアノ曲をスタインウェイからヤマハC7（Keppy’ s Yamaha C7.sf2）に変更、楽曲追加

v0.1.1 - HTML圧縮がされない場合があったのを修正、alert画面に余計なHTMLが出力されていたのを修正、alert画面を出力していないのに、マウス等でBEEP音が鳴るのを修正

v0.1.0 - BGMとアスキーアートをプレビューできるようにした

v0.0.97 - デザインの調整、JavaScript手動圧縮

v0.0.96 - 警告画面とIPビュアーにおいて、JavaScriptのuserAgentDataでも取得したユーザーエイジェントを表示するようにした、その他メッセージ変更

v0.0.95 - 警告画面中、マウスカーソルを消せるようにした

v0.0.94 - 警告画面中、マウス移動、キーボード押下などでまるで端末が不具合を起こしたかのようにBEEP音を鳴らせるようにした

v0.0.93 - メッセージの変更（1個sprintfの%sが足りなかった）、audioディレクトリの取得方法の変更

v0.0.92 - 警告のデザインを追加

v0.0.91 - 高速版革命のエチュードを追加、その対応のためにリダイレクトまでの時間を最大2分まで設定できるようにした

v0.0.90 - Ctrl+Shift+S(たぶん検出しない)の対応、macOSは共通コードが多いためまとめる

v0.0.89 - Chrome OS Flexで動作確認をし、スクリーンショットを取得される疑いのキーにおける検出に対応

v0.0.88 - 対象キー等を押して全部消えない（一部のみ消える）のを修正、JavaScript圧縮

v0.0.87 - 無理やり画像をダウンロードした時のデフォルト値を変更、JavaScript圧縮

v0.0.86 - UA偽装にある程度対処

v0.0.85 - jsの圧縮、黒鍵のエチュード高速版の追加

v0.0.84 - audioの音量を上げた、おやじの高音質化＆ロングバージョンの制作

v0.0.83 - Twenty Twenty-Fourで動作しなかったのを修正（テーマにsrcsetがなかった時の対処）

v0.0.82 - 赤い警告画面が大きすぎたのを修正

v0.0.81 - msx_openがループしてたのを修正、フラグにdの文字が重なってたのを修正

v0.0.80 - 警告画面（画像を除く）のデザインを設定できるようにすると共に同警告画面で音を鳴らせるようにした

v0.0.79 - jsの手動圧縮

v0.0.78 - URL転送の時間を5秒固定から選択できるようにした

v0.0.77 - installしたての環境でHTML保護を指定すると、データベースエラーになるのを修正

v0.0.76 - アンインストール時に大量のtransientを削除した

v0.0.75 - プラグインの初期化機能をようやく実装

v0.0.74 - CryptoJSがない場合に動作しなかったのを修正、Chrome OSのスクショに対応、LinuxのはWindowsのがそのまま使えそう

v0.0.73 - アンインストーラが動作しなかったのを修正、

v0.0.72 - 一部のphpcsのエラーを取り除いた

v0.0.71 - MacOSのスクショ、Windowsのsniping toolに対応（表示はされないがログは確認）、IPログリーダーのデザインがおかしかったのを修正

v0.0.69 - スマートフォンでリンクをタップしても反応ないのを修正

v0.0.68 - OGPがうまくいかなったのを修正

v0.0.67 - リダイレクト先URLを空欄にすると元の記事にリダイレクトするようにした

v0.0.66 - サニタイズしすぎて通らなくなったURLがあったのを修正、その他*.br、*.gzファイルをstatic用に配布

v0.0.65 - phpcsに叱られるのでcrypto-jsをバンドル化

v0.0.64 - 一部のphpcsを解決

v0.0.63 - 画像URLの保護がSafariでうまく動作してなかったので、Safariのみ更に遅延読み込みするようにした

v0.0.62 - スクショ阻止強化のためalertをやめjqueryで描画するようにした

v0.0.61 - PrintScreenキーに対応、スマホのスクリーンショットには非対応

v0.0.60 - 有名なキャッシュプラグインがインストールされていることを警告するようにした、もともとこのプラグインはキャッシュプラグインが有効になっていると動作しないため

v0.0.59 - 無理やりダウンロードしようとした時に実際にダウンロードされるものを指定できるようにした

v0.0.58 - ユーザーログイン時に画像が読み込めないのを修正（機能させなくした）

v0.0.56 - jsに読み込むのにおいて、srcsetが定義されていない時にundefinedになってたのを修正

v0.0.55 - ランダム数値をbin2hexからURLセーフbase64に変更、一部のモードで動くようにした

v0.0.54 - アンインストーラの自動生成、imageprotectのdata-src等を復活するための準備工事、画像URLのセッションの有効時間を設定可能にした

v0.0.53 - v0.0.52で追加した機能の軽量化＆メッセージ変更

v0.0.52 - 一時使用トークン（hash化とランダム数値）のbit数を設定できるようにした、ただしAES-256-CBCは変えようがない

v0.0.50 - add_filterを復活、オプションで切り替えられるようにした

v0.0.49 - 動かない場合があるので、HTML全体的に置き換える方式に仮変更

v0.0.48 - ダウンロードした画像に記載する警告メッセージを変更できるようにした

v0.0.47 - 画像保護時に警告画像のダウンロードを実際にできるようにした、初回表示時にうまく画像が表示されないことがあるのでセッションではなくIPアドレスをキーにした（調整中）

v0.0.46 - 何もかもCtrl+Sになるのを修正

v0.0.45 - 画像URLを保護の機能はほとんど機能しないため削除

v0.0.44 - 画像保護時、画像のトランジットが削除されていないのを修正

v0.0.43 - 複数画像があったときうまく処理がいかなかったのを修正

v0.0.42 - 画像保護をaddfilterによる処理に変更（でうまくうごいてる）、他の方式を後で加える予定、なおテーマによってはaddfilterでうまく動作しない模様

v0.0.41 - JavaScript関係のがロードされず機能しないのを修正、HTMLのキャッシュも0にしてソース表示されるのを更に阻止

v0.0.40 - 一部のphpcsのERRORを解決

v0.0.39 - HTML保護時、noscriptのメッセージを変更できるようにした

v0.0.38 - 画像データ保護で、ブラウザの初回表示のみ画像が表示されなかったのを修正

v0.0.37 - デバッガ妨害のわずかな強化

v0.0.36 - 画像の右クリック→ダウンロードを阻止するようにした、一部のサードパーティーHTMLダウンローダーでも画像がダウンロードできないことを確認

v0.0.35 - srcsetが処理されてなかったので修正

v0.0.34 - どうせなら、ってことでbase64の大文字小文字を入れ替えたりとか

v0.0.33 - 画像保護の仮対応、まだphpで完全処理をしていない

v0.0.32 - JavaScriptのプチ高機能化＆容量削減

v0.0.31 - HTMLプロテクト時のHTMLがおかしいのを修正

v0.0.30 - HTMLプロテクト時にHTMLのheadタグの内容が一部異なっていたのを修正、HTMLの比較は確認したのでこれ以上なおしきれない

v0.0.29 - 一部のセキュリティーfix

v0.0.28 - HTMLプロテクト時の挙動を修正、うまくいったと思う

v0.0.27 - HTMLソース表示等する権限を選択できるようにした

v0.0.26 - テーマによって動かないのを修正、ソース表示警告メッセージのサイズを管理画面に表示

v0.0.25 - 説明をもう少し丁寧にしてみた

v0.0.24 - ページの保存(Ctrl+S)に対応

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

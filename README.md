2020/6/24 　 h 関数は実装済み

# 課題開発用リポジトリについて

このリポジトリは課題開発用のリポジトリです。
このリポジトリは直接クローンせず、
一旦フォークしてからクローンして課題を開始してください。

## リポジトリのフォーク・クローン

このリポジトリからフォークしてクローンを行います。

MyDocker ディレクトリ直下に
lamp_practice ディレクトリを作成し
移動します。

```bash
mkdir ~/MyDocker/lamp_practice
cd ~/MyDocker/lamp_practice
```

開発課題のリポジトリをフォークして、自分のアカウントのリポジトリにします。

右上にあるフォークボタン(Fork と書かれたボタン）をクリックすると、
皆さんご自身の管理リポジトリの中に課題リポジトリのコピーが追加されます。

フォークしたリポジトリを開き、現在のディレクトリ(テキストでは lamp_practice)にクローンします。

```
git clone [リポジトリurl] .
```

各種ファイルのダウンロードが終わるまでしばらく待ちましょう。

## docker の立ち上げ

ダウンロードが終わったら、lamp_dock ディレクトリに移動し、
docker を立ち上げます。

```bash
cd lamp_dock
docker-compose up
```

しばらくの間、コンテナ構築の処理が行われます。（特に mysql コンテナの構築が終わるまでしばらく待ちます。）

なお、docker-compose up (-d オプションなし) で起動した場合には
Ctrl + C でコンテナを終了できます。

## Docker Toolbox をご利用の方へ

1. volumes の指定について、現在のディレクトリ（.）が指定されている箇所をクローンした lamp_dock ディレクトリに書き換えてください(lamp_dock 内で pwd)
2. localhost の指定については、仮想マシンの ip アドレスに読み替えてください。

## 確認

- ドキュメントルート: http://localhost:8080
- phpmyadmin: http://localhost:8888

にそれぞれアクセスし、アプリケーションのトップページ(ログイン画面)および
phpmyadmin のログイン画面が表示されることを確認しておきましょう。

(Docker ToolBox をお使いの方は、仮想マシンの ip アドレスにアクセスしてください。)

phpmyadmin でログインしようとして失敗する場合には、mysql コンテナの構築が途中の段階である可能性が高いです。
うまくいかない場合、一度コンテナを down してから、再度

```
docker-compose up -d
```

で立ち上げましょう。

## SQL によるインポート

クローンしたリポジトリの lamp_dock ディレクトリには sample.sql というインポート用の sql ファイルが含まれています。
phpmyadmin で sample データベースを選択して、「インポート」から sample.sql を選択してインポートしましょう。

## 課題開発環境のまとめ

- php7.2
- mysql5.7
- phpmyadmin

### ログイン情報

管理者としてログイン

- id: admin
- pass: admin

一般ユーザーとしてログイン

- id: sampleuser
- pass: password

### docker の起動・停止

~/MyDocker/lamp_practice/lamp_dock ディレクトリに移動し、

```
docker-compose up -d
```

でコンテナを起動します。

```
docker-compose down
```

で停止、コンテナ削除が可能です。

```
docker exec -it lamp_dock_php_1 bash
```

でコンテナ内を bash で操作できます。

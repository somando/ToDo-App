# PHP ToDo App

PHPで動作するToDoリストアプリケーションです。

## 機能概要

- フォルダに分けたタスク管理
- フォルダごとに共有メンバーを設定
- アサイン機能

## 使用技術

- PHP
- MariaDB
- Docker
- Docker Compose

## MariaDB 接続情報

| 項目 || 値 |
| :--- | :--- | :--- |
| ホスト名 | Dockerネットワーク内 | db |
| ホスト名 | ホストマシン | localhost |
| ポート番号 || 3306 |
| ルートユーザー名 || root |
| ルートユーザーパスワード || root |

## Dockerについて

このプロジェクトではDockerおよびDocker Composeを使用しています。

### コンテナ構成

コンテナは以下の3つで構成されています。

- Appコンテナ
- DBコンテナ
- Ubuntuコンテナ

### Appコンテナ

Appコンテナは、[php:apache (Docker Hubへのリンク) ](https://hub.docker.com/layers/library/php/apache/images/sha256-f722d3f411b2951405044dfe1c6a7ffd2bbd8662f4b7cfd7ab162974767a38a4)イメージをもとにしています。

Dockerfileでは、MySQLを使用するための拡張機能をインストールしています。

docker-compose.ymlと同じ階層を/var/www/htmlにバインドマウントしています。

### DBコンテナ

DBコンテナは、[mariadb (Docker Hubへのリンク) ](https://hub.docker.com/layers/library/mariadb/latest/images/sha256-04d70a5a9b401d1513b2d03cc446b3a375f4b9ce583c727f7dce8b74b3fded94)イメージをそのまま使っています。

MariaDBのデータはDockerボリュームに保管しています。

### Ubuntuコンテナ

Ubuntuコンテナは、[ubuntu (Docker Hubへのリンク) ](https://hub.docker.com/layers/library/ubuntu/latest/images/sha256-6e75a10070b0fcb0bead763c5118a369bc7cc30dfc1b0749c491bbb21f15c3c7)イメージをもとにしています。

Dockerfileでは、MySQLを接続するクライアントとDB作成の定義ファイルをコピーしています。

## 使い方

以下のコマンドを実行して、アプリケーションを起動します。

予め、Dockerがインストール、Docker Engineが起動している必要があります。

### 1. Docker Composeの起動

以下のコマンドを実行して、Docker Composeを起動します。

``` bash
docker compose up -d
```

初回起動時はイメージのビルドに時間がかかります。

### 2. データベース・テーブルの作成（初回起動時のみ）

以下のコマンドを実行して、Ubuntuコンテナからstructure.sql内の構造をDBコンテナに作成します。

``` bash
# Ubuntuコンテナのbashに接続
docker container exec -it todo-ubuntu bash

# コンテナ内でcreate_tables.shを実行
./create_tables.sh
```
ルートユーザーのパスワードが求められるので、入力してください。

### 3. アプリケーションへアクセス

http://localhost:8080/ でアプリケーションが使用できます。

### 4. Docker Composeの終了

以下のコマンドを実行して、Docker Composeを終了します。

``` bash
docker compose down
```

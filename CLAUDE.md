# Levis PHP Framework

## プロジェクト概要
Apache・PHP 8.3+・MySQL向けの軽量MVCフレームワーク。テンプレートエンジンにTwig、マイグレーションにschemalexを使用。

## ディレクトリ構成
```
Levis/
├── index.php          # Webエントリポイント (Router クラス、Twig描画)
├── api.php            # APIエントリポイント (JSON応答)
├── web.php            # シンプルWeb用エントリポイント (PHPテンプレート描画)
├── migration.php      # DBマイグレーション実行
├── test.php           # テストランナー
├── libs/              # フレームワークコア
│   ├── config.php     # DB接続設定 (定数: DSN, USER, PASSWORD)
│   ├── helper.php     # ユーティリティ関数 (resolveFilePath, camelize, underscore)
│   ├── bootstrap.php  # 全ライブラリのrequire
│   ├── autoloader.php # spl_autoload_register によるクラスオートロード
│   ├── data_base.php  # DB クラス (PDOラッパー、クエリビルダー)
│   ├── logger.php     # Logger クラス + LogLevel enum
│   ├── session.php    # Session クラス
│   ├── params.php     # Params クラス (リクエストパラメータ)
│   └── twig_extension.php  # TwigExtension クラス
├── api/
│   ├── controllers/controller.php  # Controller 基底クラス
│   └── models/model_base.php       # ModelBase 基底クラス
├── cms/view/          # Twigテンプレート
├── test/              # テストファイル (TestBase 基底クラス)
├── bin/               # schemalex バイナリ
└── db/                # SQLスキーマファイル (マイグレーション用)
```

## コーディング規約

### PHP
- **PHP 8.3+** が必須。`declare(strict_types=1)` を全PHPファイルに記述
- パラメータ型・戻り値型・プロパティ型を必ず宣言する
- PHP 8.x機能を積極活用: enum, arrow functions, nullsafe演算子, str_starts_with, typed constants
- クラスファイル名はスネークケース (`model_base.php`)、クラス名はパスカルケース (`ModelBase`)
- `underscore()` / `camelize()` でクラス名⇔ファイル名を変換（autoloaderが使用）

### 命名規則
- コントローラー: `{Name}Controller` クラス → `{name}_controller.php` ファイル
- モデル: `{Name}` クラス → `{name}.php` ファイル
- ビュー (Twig): `cms/view/{controller}/{action}.twig`

## アーキテクチャ
- **シングルトンパターン**: DB, Logger, Session, Params は `getInstance()` or `connect()` で取得
- **ルーティング**: PATH_INFO ベース (`/controller/action` 形式)
- **API**: `index.php` の `/api/controller/action` または `api.php` の `/controller/action`
- **Web (Twig)**: `index.php` の `/controller/action` → `cms/view/controller/action.twig`
- **Web (PHP)**: `web.php` の `/controller/action` → `view/controller/action.php`

## 開発コマンド
```bash
# 構文チェック
php -l Levis/libs/data_base.php

# 全ファイル構文チェック（PHP 8.3環境が必要）
find Levis -name "*.php" ! -path "*/vendor/*" -exec php -l {} \;

# Composer依存インストール
cd Levis && composer install
```

## 注意事項
- `Levis/libs/config.php` は環境固有のDB接続情報を含む。本番値をコミットしない
- ローカル環境がPHP 8.2以下の場合、typed constants (`const string`) の構文チェックは失敗する
- vendor/ ディレクトリはgit管理外

# Levis_PHPFramework
PHP軽量フレームワークです。

## 概要
Apache・PHP・MySqlで動作する最低限の機能を備えたフレームワークです。
テンプレートエンジンに「Twig](https://twig.symfony.com)
マイグレーション機能に[shcemalex](https://github.com/schemalex/schemalex)を使用しています。

## 使い方
1. Levisディレクトリを配置します。
1. Levis/libs/config.php 内のデーターベースへの接続設定を修正します。
1. Levis/libs/twig_extension.php のAPP_URLを修正します。
1. Levis/api/controllers/ 以下にコントローラーを配置します。
1. アクセスできるか確認し、OKなら使用可能です。

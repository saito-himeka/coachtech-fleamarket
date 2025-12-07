# coachtech-fleamarket

## アプリケーション概要
- Laravel 8 を使用したフリーマーケットアプリケーションです。
- Dockerで開発環境を構築可能。

---

## 環境構築手順

1. **リポジトリをクローン**
```bash
git clone git@github.com:saito-himeka/coachtech-fleamarket.git
cd coachtech-fleamarket
```

2. **Dockerコンテナを起動**
```bash
docker-compose up -d --build
```

3. **PHPコンテナに入る**
```bash
docker-compose exec php bash
```

4. **依存パッケージをインストール**
```bash
composer install
```

5. **環境設定ファイルを作成**
```bash
cp .env.example .env
php artisan key:generate
```

6. **ストレージ・キャッシュの権限設定**
```bash
chmod -R 777 storage bootstrap/cache
```

7. **データベースをマイグレーション**
```bash
php artisan migrate
```

## 使用技術/バージョン
- laravel 8.83.29
- php 8.1.33
- nginx 1.21.1
- mysql 8.0.26


## URL
- 開発環境:http://localhost
- ユーザー登録:http://localhost/register
- phpMyAdmin:http://localhost:8080
    - ユーザー名:laravel_user
    - パスワード:laravel_pass

## ER図

erDiagram
    %% エンティティ (テーブル) の定義
    users {
        bigint id PK "ユーザーID"
        varchar name
        varchar email
        varchar profile_image
        varchar post_code "プロフィール住所"
    }
    
    items {
        bigint id PK "商品ID"
        bigint user_id FK "出品者ID"
        bigint condition_id FK "状態ID"
        varchar name
        int price
        json image_paths "複数画像パス"
    }
    
    purchases {
        bigint id PK "購入ID"
        bigint item_id FK "商品ID (Unique)"
        bigint user_id FK "購入者ID"
        tinyint payment_method
        varchar post_code "送付先"
    }
    
    comments {
        bigint id PK
        bigint item_id FK
        bigint user_id FK
        varchar comment
    }
    
    favorites {
        bigint id PK
        bigint user_id FK
        bigint item_id FK
    }
    
    conditions {
        bigint id PK
        varchar name
    }
    
    categories {
        bigint id PK
        varchar name
    }
    
    category_item {
        bigint id PK "中間テーブル"
        bigint item_id FK
        bigint category_id FK
    }
    
    %% リレーションシップの定義 (Laravel Eloquentの記法に近い表記を使用)
    
    % ユーザー (出品者) と商品 (1対多)
    users ||--o{ items : 出品_has
    
    % ユーザー (購入者) と取引 (1対多)
    users ||--o{ purchases : 購入_buys
    
    % ユーザーとコメント (1対多)
    users ||--o{ comments : コメント_posts
    
    % ユーザーといいね (多対多の中間)
    users ||--o{ favorites : いいね_likes
    
    % 商品と取引 (1対0または1) - 購入されたらSold
    items ||--|{ purchases : 取引完了_sold
    
    % 商品と状態 (1対多) - マスタ
    conditions ||--o{ items : 状態_has_status
    
    % 商品とコメント (1対多)
    items ||--o{ comments : コメント対象_has
    
    % 商品といいね (多対多の中間)
    items ||--o{ favorites : いいね対象_is_liked
    
    % 商品とカテゴリ (多対多の中間)
    items }|--o{ category_item : 複数カテゴリ
    categories }|--o{ category_item : カテゴリ

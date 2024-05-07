## Laravel11 フォロー・フォロワーのリレーション

フォロー・フォロワーとは**User同士の多対多のリレーション**であると理解していればLaravelのリレーションで簡単に実現できる。

## 中間テーブル
`-p`(`--pivot`)を付けてFollowはPivotにしている。マイグレーションも同時に作成。
```shell
php artisan make:model Follow -pm
```
これで作るとテーブル名は`follow`になる。中間テーブルの本来の命名規約は`user_role`のような形だけど今回は`user_user`になって分かりにくいのでLaravelが自動で作った`follow`のまま使う。
テーブル名もモデル名も自由なのでなんでもいい。

## マイグレーション
```php
    public function up(): void
    {
        Schema::create('follow', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('follow_id')->comment('フォローしているユーザーID');
            $table->timestamps();
        });
    }
```

## リレーションの設定
一番重要なリレーションの設定は`app/Models/User.php`

中間テーブルが規約と違うので個別に設定が必要だけど単なる多対多のリレーション。

```php
    /**
     * フォローしているユーザー.
     */
    public function followings(): BelongsToMany
    {
        //tableはテーブル名だけでなくモデルで指定してもいい

        return $this->belongsToMany(
            related: User::class,
            table: Follow::class,
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'follow_id'
        )->withTimestamps()
            ->using(Follow::class);

        //using()は使っても使わなくてもサンプル程度では影響ない
    }

    /**
     * フォローされているユーザー.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: Follow::class,
            foreignPivotKey: 'follow_id',
            relatedPivotKey: 'user_id'
        )->withTimestamps();
    }
```

## 具体的な使い方はテストを参照

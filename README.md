# Install
```
composer require kitamula/kitchen
php artisan vendor:publish --provider="Kitamula\Kitchen\KitchenServiceProvider"
```

# 機能

## Config
### Basic認証ミドルウェア
.envで次の設定値を設定することで、RouteMiddleware "kitchen.basicauth" の設定されたルートに対しBasic認証がかかる。

BASICAUTH_TO_ATをfalseとすることで解除。
年月日または日時を入力することでBasic認証がかかる。
年月日を入力: 当日の23:59:59まで
日時(年〜秒): 指定の時刻まで
```
BASICAUTH_USER="alpha"
BASICAUTH_PASSWORD="beta"
BASICAUTH_TO_AT="2022-12-03 10:08:00"
```
### IP制限ミドルウェア
.envで次の設定値を設定することで、RouteMiddleware "kitchen.ip_restriction" の設定されたルートに対しBasic認証がかかる。

IP_RESTRICTION_TO_ATをfalseとすることで解除。
年月日または日時を入力することでBasic認証がかかる。
年月日を入力: 当日の23:59:59まで
日時(年〜秒): 指定の時刻まで

IP_RESTRICTION_ALLOW_IPSはカンマ区切りでGlobalIPを指定する。
*を指定することで期間設定に関わらず全IPを許可する
```
IP_RESTRICTION_ALLOW_IPS=*,::1,127.0.0.1,202.214.242.193
IP_RESTRICTION_TO_AT="2022-12-03 10:08:00"
```

## Model
```
use \Scopable
```
### columnGroup メソッド
複数カラムを1つのグループとして取得する
```
public function getUrlsAttribute()
{
    return $this->columnGroup(3,
        ['url'=>'url_*', 'text'=>'url_*_text', 'is_newtab'=>'url_*_is_newtab'],
        ['url_*']
    );
}
```

### scopeInTerm
カラムの型がDateかDateTimeかによって判定を自動的に変更する。
Migration>
```
Article::inTerm()->get();
// カラム名を指定する場合
Article::inTerm($fromColumn, $toColumn)->get();
```
### scopeWord
検索文字列と検索対象カラムの配列を渡すことでLIKE検索を行う。
```
Article::word($searchText, ['title', 'detail', 'keyword', ...])
```

### scopePublished
scopeInTermおよびscopeDisplayedを基準に公開状況を判定する
### scopeDisplayed
is_displayカラムを基準に公開状況を判定する

### is_published プロパティ
scopePublishedを基準に公開状況を返すプロパティをModelに追加する
```
Article::find(1)->is_published
```

## Migration
### termDate|termDateTime
公開期間として利用するカラムの定義
from_atとto_atカラムが追加される。
本ライブラリ、Scopableに定義されている scopeInTerm ではfrom_atとto_atカラムがデフォルトの公開期間カラムとして自動的に認識されるため、特にこだわりのない場合はこれを利用すること。
```
$table->termDate();
// または
$table->termDateTime();
```

## Blade
### ストレージへのPATHを返す関数
```
@storage($path)
```

### 改行を`<br>`に変換する関数
```
@nl2br($text)
```

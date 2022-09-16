# baserCMS-OperationPlugin
baserCMS にてメインサイト、各サブサイトのコンテンツ管理にアクセスする許可をユーザーグループ単位で権限設定するプラグインです。

## Installation
下記の手順で Operation プラグインを baserCMS にインストールします。
1. ソースコードをダウンロードします。
1. ダウンロードした zip ファイルを解凍し、フォルダを Operation という名前に変更します。
1. Operation フォルダを /app/Plugin/ 直下に設置します。
1. baserCMS 管理システムにログインし、プラグイン管理で Operation プラグインを有効化します。
1. メインサイト、各サブサイトの編集画面で権限を設定します。

## Config
Operation プラグインの挙動を設定できます。

### /Operation/Config/setting.php
```
$config['Operation'] = [
    'admin' => [
        'adminsName' => [
            'admins'
        ],
        'allowedAdminAllOperation'     => TRUE,
        'allowedAllUserGroupUploads'   => FALSE,
        'allowedAllUserGroupDblogs'    => FALSE,
        'allowedAllUserGroupBlogPosts' => TRUE
    ]
];
```
### Operation.admin.adminsName
管理者権限に設定したいユーザーグループ名を配列で設定します。  
ここで設定されたアカウントが後述の `Operation.admin.allowedAdminAllOperation` の対象になります。

### Operation.admin.allowedAdminAllOperation
管理者権限ユーザーグループによる全サイトのコンテンツ管理を一律許可する設定です。
- TRUE : 全サイトでのコンテンツ管理を許可します。
- FALSE : コンテンツ管理の許可を各サイト毎に設定する必要があります。

### Operation.admin.allowedAllUserGroupUploads
アップロード管理に表示するファイルを設定します。  
`Operation.admin.allowedAdminAllOperation` が TRUE の場合、管理者権限ユーザーグループは全アカウントのファイルを表示します。
- TRUE : 全アカウントのファイルを表示します。
- FALSE : 同じユーザーグループのアカウントのファイルのみを表示します。

### Operation.admin.allowedAllUserGroupDblogs
ダッシュボードの最近の動きに表示するログを設定します。  
`Operation.admin.allowedAdminAllOperation` が TRUE の場合、管理者権限ユーザーグループは全アカウントのログを表示します。
- TRUE : 全アカウントのログを表示します。
- FALSE : 同じユーザーグループのアカウントのログのみを表示します。

### Operation.admin.allowedAllUserGroupBlogPosts
ブログ記事一覧に表示するブログ記事を設定します。  
`Operation.admin.allowedAdminAllOperation` が TRUE の場合、管理者権限ユーザーグループは全アカウントのブログ記事を表示します。
- TRUE : 全アカウントのブログ記事を表示します。
- FALSE : 同じユーザーグループのアカウントのブログ記事のみを表示します。
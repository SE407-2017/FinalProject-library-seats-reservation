# Library Reservation
## Guide
### Development pre-request

必须:

In Windows:
- PHP Composer (https://getcomposer.org/) (推荐中国镜像: https://pkg.phpcomposer.com/#how-to-install-composer)

推荐开发环境:

In Windows:
- xampp (https://www.apachefriends.org/xampp-files/5.6.31/xampp-win32-5.6.31-0-VC11-installer.exe)
- PHPStorm (https://www.jetbrains.com/phpstorm/)

确保php.exe所在目录已添加到PATH环境变量

### Getting start

(Windows下建议在Powershell或Git Bash中进行操作)

1. fork这个代码仓库

2. 将代码clone到本地
    ```
    git clone https://github.com/你的用户名/FinalProject-library-reservation
    ```

3. 切换到项目目录
    ```
    cd FinalProject-library-reservation
    ```

4. 安装依赖包
    ```
    composer install
    ```

5. 创建环境配置文件
    ```
    mv .env.example .env
    ```
6. 打开xampp控制面板, 点击Apache->Config->Apache (httpd.conf), 找到
    ```
    Listen 80
    ```
    在其下方添加一行:
    ```
    Listen 8081
    ```
    保存退出, 再点击Apache->Config-> Browse [Apache], 打开conf\extra\httpd-vhosts.conf, 添加如下行:
    ```
    <VirtualHost *:8081>
        ServerAdmin webmaster@localhost
        DocumentRoot "[项目目录]/public"
        ServerName localhost
        ErrorLog "logs/localhost-error.log"
        CustomLog "logs/localhost-access.log" common
    </VirtualHost>
    ```
    保存退出, 重启Apache。
    
    xampp控制面板中点击MySQL->Start, MySQL->Admin, 左侧点击New, Database name填library-reservation, 点击Create.

7. 用PHPStorm打开项目

8. 打开.env文件, 修改如下行 (若使用xampp可直接复制以下配置):
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=library-reservation
    DB_USERNAME=root
    DB_PASSWORD=
    ```

9. 迁移数据库:
    ```
    php artisan migrate
    ```

10. 生成key:
    ```
    php artisan key:generate
    ```

11. 打开浏览器, 访问http://localhost:8081, enjoy!

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

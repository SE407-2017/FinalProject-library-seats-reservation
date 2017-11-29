# Library Reservation
![](https://api.travis-ci.org/SE407-2017/FinalProject-library-seats-reservation.svg?branch=master)

项目已基本完成。以下为用户手册，项目部署请详见[guide.md](guide.md)。
## User guide
体验地址：[https://library.shinko.love/](https://library.shinko.love/)。

### 开始使用
* 打开上述地址，网站将自动跳转到jAccount登录，使用jAccount账号登录后即可访问网站

    <img src="https://user-images.githubusercontent.com/7235968/33252194-99025d32-d377-11e7-8c60-751756ab77c2.png" height="200" />

* 此时您可以预约座位或管理您现有的预约。

    <img src="https://user-images.githubusercontent.com/7235968/33252228-c351b6b4-d377-11e7-9f2f-2fc0a3ce04f6.png" height="200" />

* **预约座位：**
    1. 选择您需要的楼层
    
        <img src="https://user-images.githubusercontent.com/7235968/33252289-24ccf548-d378-11e7-88f1-87c2ecbe016f.png" height="200" />
    
    2. 选择您需要的自习桌，单击“预约”按钮
        
        <img src="https://user-images.githubusercontent.com/7235968/33252313-690e77f4-d378-11e7-8d38-17c3c50d51fb.png" height="200" />
    
    3. 选择座椅号，选择预计到达时间，提交预约
    
        <img src="https://user-images.githubusercontent.com/7235968/33252360-ad7b1aa0-d378-11e7-844d-62a045bb811e.png" height="200" />

    4. 如果您当前可以预约该座位，您将会被跳转至“所有预约”页面。否则，您将被提示您为何在此时无法进行预约。
    
        <img src="https://user-images.githubusercontent.com/7235968/33252880-b04cc7d0-d37b-11e7-8b42-2d315e0c81b8.png" height="200" />
    
        <img src="https://user-images.githubusercontent.com/7235968/33252901-ceae822c-d37b-11e7-8a13-e4046740fa86.png" height="200" />

    5. 如果您有预约无法成行，可以在“所有预约”页面取消该预约。每天可以取消的预约**不超过3次**。

* **到馆签到：**
    （为演示方便，可以在[测试页面](https://library.shinko.love/test/qr/)生成座位二维码。
    1. 如图，生成二维码
    
        <img src="https://user-images.githubusercontent.com/7235968/33253006-5dee7852-d37c-11e7-903c-f54a39a3970e.png" height="200" />

    2. 使用手机微信扫描二维码，进入入座签到程序
    
        <img src="https://user-images.githubusercontent.com/7235968/33253077-c2b59c3e-d37c-11e7-86ef-b7fefacf5562.png" height="200" />

    3. 若要离开座位，点击“离开”即可
    
        <img src="https://user-images.githubusercontent.com/7235968/33253126-ea012b64-d37c-11e7-9894-31f81847a005.png" height="200" />


## Thanks to
* [Laravel](https://laravel.com/)
    > The PHP Framework For Web Artisans

* [Hitokoto 一言](http://hitokoto.cn)
    > 动漫也好、小说也好、网络也好，不论在哪里，我们总会看到有那么一两个句子能穿透你的心。

* **马颖华老师及助教苏宇老师**

* "图书馆预约占座系统"团队所有成员
## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


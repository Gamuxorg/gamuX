# gamuX
[![996.icu](https://img.shields.io/badge/link-996.icu-red.svg)](https://996.icu)  
linuxgame.cn下一代主题，框架基于wordpress5.x，后端使用php 7.x，前端使用Vuejs。

## 体验

```shell
git clone https://github.com/Gamuxorg/gamuX.git
```

将文件夹放在wordpress的wp-content/themes里，plugins文件夹的内容拷贝到wp-content/plugins，在wordpress的后台启用gamux-slide-url插件、切换主题即可。

在线体验地址为<http://next.linuxgame.cn>

## 贡献

本地修改、调试代码没问题后，pull request即可。

## todo list

* [x] vuejs实现的前端
* [x] 重写github登录（检查登录错误、出现重号的问题）
* [x] 增加更多登录方式(wechat/QQ等)
* [x] 前端基于restful api实现更好的文章列表
* [x] 投稿时后台一键调取steam游戏数据
* [X] 独立的文档页、新闻页
* [X] 消息提示功能，有人@或回复消息，前端有提示
* [x] 更精确的数据统计
* [x] 更人性化的评论系统
* [x] 兼容wordpress 5.x
* [ ] 优化后台数据库表
* [ ] 编写网站的隐私政策
* [x] 修复github用户获取昵称不正确bug，当前部分用户显示为github_xxxx
* [x] 轮播和文章中的图片进行分离，专门为轮播图片在后台增加上传模块（只贴url即可）
* [x] 支持多个购买链接(需要更新数据库meta_key)
* [x] 下载链接支持多cpu平台
* [x] 限制非linux平台用户，只能获取网盘地址
* [x] 下载链接中增加一栏“备注”
* [x] 实现搜索功能，并优化搜索算法
* [x] 下载统计API
* [ ] 完全重实现的后台（主要是文章编辑界面）

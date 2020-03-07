### local-movie-bundle
---


*This is a symfony4|5 bundle;* 

Install it:

```bash
composer req jayson/local-movie-bundle
# However, I just push it to GitHub. :)
```

Just do it!

Add the file `local_movie.yaml` in your project at `config/packages`,And write configuration as follows:

```yaml
local_movie:
    storage_dir:
        - 'D:\movies'
        - 'G:\迅雷下载'
        - 'H:\迅雷下载'
        - 'E:\迅雷下载'
        - 'F:\GameDownload'
```

Add follows code in `config/routes.yaml` :) The crudest way.
```yaml
index:
    path: /
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::redirectAction
    defaults:
        route: 'local.movies'
```

That's all!

多说两句，根目录附上一个vbs文件，能看懂的人就知道，这是利用`symfony/web-server-bundle`的一个命令启动`symfony`项目的小工具,按你的实际情况改一下下面这段代码：

```
command = volume + "Applications/Scoop/apps/php73 " + currentPath + "/app/bin/console server:run " + webAddress + ":" + webPort

'volume 是 windows系统中的盘符
'currentPath 是symfony项目的根目录路径

```
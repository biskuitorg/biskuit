[![biskuit Banner](https://raw.githubusercontent.com/biskuitorg/biskuit-assets/ac2e599b0a83a4ed31d1fc74f47b340a96f61322/brand/biskuit-cover.png)](https://github.com/biskuitorg/biskuit)

> This project IS NOT discontinued.
> We are working on the conversion from uikit2 to uikit3. We are a small team and do this as a hobby.

# Biskuit CMS
[![Discord](https://img.shields.io/discord/682566394222477378)](https://discord.gg/aBdqDcE)

Bis[ku]it is a modular and lightweight CMS built with Symfony components and Vue.js.

This unofficial version of Pagekit was created to solve various problems. Mainly born for linuxhub.it, a portal that uses PageKit.

This project is taking a different path from that of official pagekit. The project is called Bis[ku]it and will not be compatible with future pagekit updates.

Feel free to use it.

---

## Install from package
Go to the [Releases](https://github.com/biskuitorg/biskuit/releases) section and download latest package.

### WebHosting ready
Decompress data from archive to you web storage then follow instructions from browser.

### Classic (access to composer)
Decompress data from archive to your web path and run composer installation:
```
composer install
```
then follow instructions from browser.

## Install from source
```
git clone https://github.com/biskuitorg/biskuit.git
cd biskuit-master
```
Install dependencies using composer and npm:
```
composer install
npm install
```
Continue installing from browser.

## Nginx configuration (optional)
In Nginx the .htaccess files are not supported, below the instructions for the Nginx configuration:
```
location / {
    location ~* ^.+\.(jpeg|jpg|png|gif|bmp|ico|svg|css|js)$ {
        expires     max;
    }
    try_files $uri $uri/ /index.php$is_args$args;
}

location ~ ^/index\.php(/|$) {
    fastcgi_pass   YOUR_SOCK;
    fastcgi_split_path_info ^(.+\.php)(/.*)$;
    include /etc/nginx/fastcgi_params; # this may be different
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_param HTTP_MOD_REWRITE On;
}

location ~ \.php$ {
    return 404;
}

```

## Caddy 2 configuration (optional)
```
mydomain.ex {                                                             
        root * /full_path_to_biskuit                  
        php_fastcgi * unix//run/php-fpm/www.sock                          
        encode gzip                                                       
        file_server                                                       
}
```
## CLI

Pagekit offers a set of commands to run usual tasks on the command line. You can see the available commands with
```
./biskuit --help
```

## Documentation
You can read the documentation [here](https://github.com/biskuitorg/docs).

## Contributing

Finding bugs, sending pull requests, translating Pagekit or improving our docs -
any contribution is welcome and highly appreciated. To get started, head over
to our [contribution guidelines](.github/CONTRIBUTING.md). Thanks!

## Dev version
You can try the new features from the dev branch.

## Credits

[Pagekit](http://www.pagekit.com) by YOOtheme [MIT license](LICENSE)  
[Half Dome Photo](http://www.youseethenew.com/landscape-outdoors/) by Brendan Lynch / [CC BY](http://creativecommons.org/licenses/by-nd/4.0/)  
Thanks to [Ruvim Miksanskiy](https://www.pexels.com/it-it/@digitech) for cover wallpaper

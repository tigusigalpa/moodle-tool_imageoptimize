# Image optimize/compress tool for Moodle

![tool_imageoptimize_logo](https://user-images.githubusercontent.com/2721390/154801676-deadcba3-ab6c-4eb1-9307-218d5fb34c1f.jpg)

With this admin tool plugin it is possible to save your hard disk space through optimization (compressing) uploaded images sizes (up to 80%) without huge quality cut. It works on a Moodle based ___create___ and ___update___ [hooks](https://docs.moodle.org/dev/Callbacks#Types_of_callbacks_in_Moodle). You just have to install some server tools on your server.

## Requirements

### Moodle

Moodle version **3.8+**

> Working with Moodle **3.8+** is possible because of **[MDL-35773](https://github.com/moodle/moodle/commit/94c71056a31327d6ef121ff7bb2a2cb15675b0c4#diff-34fd265656b62b1b63bfd0dc74c51c6d3b74d1b150205c1375f06247dd2696d2)**.

### Server

UNIX backed OS (_not Windows or MacOS for the moment_): **Ubuntu/Debian, CentOS, Fedora**.

### PHP

PHP version **7.2.0+**

## Install

### Server packages

#### Debian/Ubuntu

```$bash
sudo apt-get install jpegoptim

sudo apt-get install optipng

sudo apt-get install gifsicle

sudo apt-get install webp

sudo apt-get install pngquant
```

#### Fedora/RHEL/CentOS

```$bash
sudo dnf install jpegoptim

sudo dnf install optipng

sudo dnf install gifsicle

sudo dnf install libwebp-tools

sudo dnf install pngquant
```
### Check Setup.
To check if, e.g. on the cron job server (web server cluster), all prerequisites are met, you can run the following CLI script on the console:

```$bash
php admin/tool/imageoptimize/cli/check_installed_modules.php
``` 
The results of this script are stored in config and affects the display in settings.php. Even if the cron jobs are executed via a separate server, the settings will show whether all libraries are installed.

### PHP

PHP core [exec()](https://www.php.net/manual/en/function.exec.php) function enabled (excluded from [disable_functions](https://www.php.net/manual/en/ini.core.php#ini.disable-functions) directive in **php.ini**)

### Code install

1. Download with any cases below
    1. Go to [Moodle plugin page](https://moodle.org/plugins/tool_imageoptimize) and download ZIP file with the latest version
    2. [Download here](https://github.com/tigusigalpa/moodle-tool_imageoptimize/archive/master.zip)
2. As an administrator go this way: ```Site administration -> Plugins -> Install plugins```
3. Drop or choose a ZIP file to the **ZIP package** field, click **Install plugin from the ZIP file**

## Credits

1. The plugin using composer package [spatie/image-optimizer](https://github.com/spatie/image-optimizer). Thanks to [Freek Van der Herten](https://github.com/freekmurze)
2. [jpegoptim](http://freshmeat.sourceforge.net/projects/jpegoptim)
3. [OptiPNG](http://optipng.sourceforge.net/)
4. [Gifsicle](http://www.lcdf.org/gifsicle/)
5. [WebP](https://developers.google.com/speed/webp)

## Languages

1. English
2. Russian
3. Spanish

## License

Moodle admin tool ImageOptimize is licensed under [GNU General Public License v3 (or later)](https://www.gnu.org/licenses/gpl-3.0.en.html).

## Author and contributors

[Igor Sazonov](https://twitter.com/tigusigalpa) ([sovletig@gmail.com](mailto:sovletig@gmail.com))

[Robert Schrenk](https://twitter.com/rschrenk)

[David Bogner](https://github.com/dasistwas)

[Georg](https://github.com/GGeorggg) ([georg-github.com@glas.eu.org](mailto:georg-github.com@glas.eu.org))

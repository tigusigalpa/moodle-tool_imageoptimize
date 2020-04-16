# Image optimize/compress tool for Moodle

Save your hard disk space. Image optimize/compress tool is a Moodle plugin that makes sizes of uploaded images files smaller (from 10% to 80%) without cutting a quality on a Moodle based ___create___ and ___update___ [hooks](https://docs.moodle.org/dev/Callbacks#Types_of_callbacks_in_Moodle).

## Requirements

### Moodle

Moodle version **3.8+**

> Working with Moodle **3.8+** is possible because of **[MDL-35773](https://github.com/moodle/moodle/commit/94c71056a31327d6ef121ff7bb2a2cb15675b0c4#diff-c3687fe83effbd91c8bf18e648948632)**.

### Server

UNIX backed OS (_not Windows or MacOS for the moment_): **Ubuntu/Debian, CentOS, Fedora**.

### PHP

PHP version **7.0.0+**

## Install

### Server packages

#### Debian/Ubuntu

```$bash
sudo apt-get install jpegoptim

sudo apt-get install optipng

sudo apt-get install pngquant

sudo apt-get install gifsicle

sudo apt-get install webp
```

#### Fedora/RHEL/CentOS

```$bash
sudo dnf install jpegoptim

sudo dnf install optipng

sudo dnf install pngquant

sudo dnf install gifsicle

sudo dnf install libwebp-tools
```

### PHP

PHP core [exec()](https://www.php.net/manual/en/function.exec.php) function enabled (excluded from [disable_functions](https://www.php.net/manual/en/ini.core.php#ini.disable-functions) directive in **php.ini**)

### Code install

1. Download with any cases below
    1. Go to [Moodle plugin page](https://moodle.org/plugins/tool_imageoptimize) and download ZIP file with the latest version
    2. [Download here](https://github.com/tigusigalpa/moodle-admin_tool_imageoptimize/archive/master.zip)
2. As an administrator go this way: ```Site administration -> Plugins -> Install plugins```
3. Drop or choose a ZIP file to the **ZIP package** field, click **Install plugin from the ZIP file**

## Credits

1. The plugin using composer package [spatie/image-optimizer](https://github.com/spatie/image-optimizer). Thanks to [Freek Van der Herten](https://github.com/freekmurze)
2. [jpegoptim](http://freshmeat.sourceforge.net/projects/jpegoptim)
3. [OptiPNG](http://optipng.sourceforge.net/)
4. [pngquant](https://pngquant.org/)
5. [Gifsicle](http://www.lcdf.org/gifsicle/)
6. [WebP](https://developers.google.com/speed/webp)

## Languages

1. English
2. Russian
3. Spanish

## License

MIT License

## Author

[Igor Sazonov](https://twitter.com/tigusigalpa) ([sovletig@gmail.com](mailto:sovletig@gmail.com))
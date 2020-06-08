<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_imageoptimize', language 'ru'
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Оптимизация картинок';

$string['files_types'] = 'Типы файлов';
$string['files_types_desc'] = 'Выберите типы файлов к которым будет применяться оптимизация';
$string['create_desc'] = 'Применять оптимизацию при первой загрузке файла';
$string['update_desc'] = 'Применять оптимизацию при обновлении файла';
$string['more_than'] = 'Оптимизировать файлы больше, Кб';
$string['os_warning'] = 'На данный момент плагин не поддерживает работу с операционной системой вашего сервера: <strong>{$a}</strong>';
$string['exec_warning'] = 'Для функционирования данного плагина, необходимо произвести некоторые настройки вашего сервера
                        <ol>
                            <li>Включить php-функцию <strong><a href="https://www.php.net/manual/ru/function.exec.php" 
                            target="_blank">exec()</a></strong>
                                <ol>
                                    <li>найдите путь к файлу конфигурации <strong>php.ini</strong></li>
                                    <li>отредактируйте в этом файле список отключенных функций в директиве <strong>
                                    <a href="https://www.php.net/manual/ru/ini.core.php#ini.disable-functions" 
                                    target="_blank">disable_functions</a></strong>, а именно удалите из списка 
                                    <strong>exec</strong> и сохраните изменения</li>
                                    <li>перезагрузите php</li>
                                </ol>
                            </li>
                            <li>Установите на сервер необходимые для сжатия пакеты для каждого из типов изображений</li>
                        </ol>';
$string['info_title'] = 'Для расширения функционала сжатия изображения формата <strong>{$a}</strong>, вы можете установить дополнительные пакеты для сжатия';
$string['warning_title'] = 'Для появления опции, установите на ваш сервер один из пакетов или все вместе';
$string['jpegoptim'] = '<strong><a href="http://freshmeat.sourceforge.net/projects/jpegoptim" target="_blank">JpegOptim</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install jpegoptim</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install jpegoptim</code></li>
                        </ul>';
$string['optipng'] = '<strong><a href="http://optipng.sourceforge.net/" target="_blank">OptiPNG</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install optipng</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install optipng</code></li>
                        </ul>';
$string['gifsicle'] = '<strong><a href="http://www.lcdf.org/gifsicle/" target="_blank">Gifsicle</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install gifsicle</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install gifsicle</code></li>
                        </ul>';
$string['webp'] = '<strong><a href="https://developers.google.com/speed/webp" target="_blank">WebP</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install webp</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install libwebp-tools</code></li>
                        </ul>';

$string['privacy:metadata'] = 'Плагин инструмента администрирования оптимизации картинок не хранит никаких персональных данных.';

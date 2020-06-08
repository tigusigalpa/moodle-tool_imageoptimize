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
 * Strings for component 'tool_imageoptimize', language 'es'
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Optimización de imagen';

$string['files_types'] = 'Tipo de archivos';
$string['files_types_desc'] = 'Seleccione los tipos de archivo a los cuales la optimización será aplicada';
$string['create_desc'] = 'Aplicar la optimización a un archivo cargado por primera vez';
$string['update_desc'] = 'Aplicar la optimización a un archivo actualizado';
$string['more_than'] = 'Optimizar más archivos que en él KB';
$string['os_warning'] = 'Por el momento, el complemento no permite trabajar a el sistema operativo que tiene tu servidor: <strong>{$a}</strong>';
$string['exec_warning'] = 'Para hacer el funcionamiento de este complemento, necesitamos hacer algunas configuraciones en tu servidor
                        <ol>
                            <li>Activar las funciones esenciales de PHP<strong>
                            <a href="https://www.php.net/manual/es/function.exec.php" 
                            target="_blank">exec()</a></strong>
                                <ol>
                                    <li>localizar la ruta del archivo de configuración <strong>php.ini</strong></li>
                                    <li>Revisar la lista de las funciones deshabilitadas en esa directiva<strong>
                                    <a href="https://www.php.net/manual/es/ini.core.php#ini.disable-functions" 
                                    target="_blank">disable_functions</a></strong> el archivo, eliminar de la lista  
                                    <strong>exec</strong> y guardar los cambios</li>
                                    <li>reiniciar php</li>
                                </ol>
                            </li>
                            <li>Instalar la paquetería necesaria para la compresión de cada tipo de imagen del servidor</li>
                        </ol>';
$string['info_title'] = 'Para extender la función del formato de compresión, <strong>{$a}</strong>, se podría instalar paquetería adicional';
$string['warning_title'] = 'Instalar uno de los paquetes en el servidor o todos juntos';
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

$string['privacy:metadata'] = 'El complemento de la herramienta para la optimización de imagen no almacena ningún dato personal.';

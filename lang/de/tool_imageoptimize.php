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
 * Strings for component 'tool_imageoptimize', language 'de'
 *
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Bild Optimierung';

$string['files_types'] = 'Dateitypen';
$string['files_types_desc'] = 'Wählen Sie die Dateitypen aus, auf die die Optimierung angewendet werden soll';
$string['create_desc'] = 'Anwenden der Optimierung beim ersten Hochladen von Dateien';
$string['update_desc'] = 'Anwenden der Optimierung auf ein Datei-Update';
$string['more_than'] = 'Optimieren von Dateien größer als x KB';
$string['os_warning'] = 'Derzeit unterstützt das Plugin die Arbeit mit dem Betriebssystem Ihres Servers nicht:<strong>{$a}</strong>';
$string['exec_warning'] = 'Für die Funktion dieses Plugins müssen Sie einige Einstellungen Ihres Servers vornehmen
                        <ol>
                            <li>Aktivieren Sie die PHP-Core-Funktion<strong>
                            <a href="https://www.php.net/manual/en/function.exec.php"
                            target="_blank">exec()</a></strong>
                                <ol>
                                    <li> Finden Sie den Pfad zur Konfigurationsdatei <strong> php.ini </strong> </li>
                                    <li> Bearbeiten Sie die Liste der deaktivierten Funktionen in dieser Anweisung <strong>
                                    <a href = "https://www.php.net/manual/en/ini.core.php#ini.disable-functions">
                                    target = "_ blank"> disable_functions </a> </strong> in dieser Datei aus der Liste löschen
                                    <strong> exec </strong> und speichern Sie die Änderungen </li>
                                    <li> PHP </em> neu starten
                                </ol>
                            </li>
                            <li>Installieren Sie die für die Komprimierung erforderlichen Pakete für jeden Image-Typ auf dem Server</li>
                        </ol>';
$string['info_title'] = 'Um die Formatkomprimierungsfunktion <strong> {$a} </strong> zu erweitern, können Sie zusätzliche Pakete für die Komprimierung installieren';
$string['warning_title'] = 'Installieren Sie eines der Pakete auf Ihrem Server oder alle zusammen, damit die Option verwendet werden kann:';
$string['jpegoptim'] = '<strong> <a href="http://freshmeat.sourceforge.net/projects/jpegoptim" target="_blank"> JpegOptim </a> </strong>
                        <ul>
                            <li> <strong> Debian / Ubuntu </strong>: <code> sudo apt-get install jpegoptim </code> </li>
                            <li> <strong> Fedora / RHEL / CentOS </strong>: <code> sudo dnf jpegoptim </code> </li> installieren
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

$string['privacy:metadata'] = 'Das Bildoptimierungs-Tool-Plugin speichert keine persönlichen Daten.';

// Strings for optimization task.
$string['backgroundoptimizingheading'] = 'Einstellungen zur Hintergrundoptimierung';
$string['cantfindfile'] = 'Die Datei konnte nicht im Dateispeicher gefunden werden.';
$string['cantcopyfile'] = 'Die Datei konnte nicht gespeichert werden.';
$string['enablebackgroundoptimizing'] = 'Optimierung im Hintergunrd';
$string['enablebackgroundoptimizing_desc'] = 'Wenn die Hintergrundoptimierung aktiviert ist, erfolgt beim Hochladen keine Optimierung.';
$string['filecheckfailed'] = 'Die Dateiprüfung ist fehlgeschlagen.';
$string['taskoptimize'] = 'Backgrond-Bildoptimierungstask';
$string['taskoptimize_fill_table'] = 'Befüllen der tool_imageoptimize_files Tabelle';
$string['maxchunksize'] = 'Maximale Anzahl von Dateien, die mit einem Taskaufruf verarbeitet werden.';
$string['maxchunksizeimport'] = 'Maximale Anzahl von Dateien, die mit einem Taskaufruf in die Verarbeitungstabelle geschrieben werden.';
$string['filehasnofilesize'] = 'Die Datei hat keine Dateigröße oder Dateigröße 0. Abbruch!';
$string['filesortorder'] = 'Wählen der Dateisortierung';
$string['filesortorder_id_asc'] = 'Beginnend mit den ältesten Dateien';
$string['filesortorder_id_desc'] = 'Beginnend mit den neuesten Dateien';
$string['pathnamehashchanged'] = 'Der Pathnamehash hat sich verändert. Abbruch!';
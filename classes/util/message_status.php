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
 * @created    31/01/17 06:01
 * @package    local_facial
 * @copyright  2017 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_facial\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Class message_status
 *
 * @package local_facial\util
 */
class message_status {

    /**
     * @param $texto
     *
     * @return string
     */
    public static function warning($texto) {
        return "<div class=\"alert alert-warning\">
            <i class=\"fa fa-exclamation-circle\"></i>
            $texto
        </div>";
    }

    /**
     * @param $texto
     */
    public static function print_warning($texto) {
        echo self::warning($texto);
    }

    /**
     * @param $texto
     *
     * @return string
     */
    public static function success($texto) {
        return "<div class=\"alert alert-success\">
            <i class=\"fa fa-check-circle\"></i>
            $texto
        </div>";
    }

    /**
     * @param $texto
     */
    public static function print_success($texto) {
        echo self::success($texto);
    }

    /**
     * @param        $texto
     * @param string $extraclass
     *
     * @return string
     */
    public static function info($texto, $extraclass = '') {
        return "<div class=\"alert alert-info $extraclass\">
            <i class=\"fa fa-info-circle\"></i>
            $texto
        </div>";
    }

    /**
     * @param        $texto
     * @param string $extraclass
     */
    public static function print_info($texto, $extraclass = '') {
        echo self::info($texto, $extraclass);
    }

    /**
     * @param $texto
     *
     * @return string
     */
    public static function danger($texto) {
        return "<div class=\"alert alert-danger\">
            <i class=\"fa fa-times-circle\"></i>
            $texto
        </div>";
    }

    /**
     * @param $texto
     */
    public static function print_danger($texto) {
        echo self::danger($texto);
    }
}
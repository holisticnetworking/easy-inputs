<?php
/*
Plugin Name: Testing Easy Inputs
Plugin URI: https://github.com/holisticnetworking/easy-inputs
Description: Testing and demonstrating Easy Inputs.
Version: 0.1-beta
Author: Thomas J. Belknap
Author URI: http://holisticnetworking.net
*/
/**
 * Test Plugin for EasyInputs
 *
 * @package EasyInputs
 * @author  Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license GPLv2 or later
 * @link    http://holisticnetworking.net/easy-inputs-wordpress/
 */

/*  Copyright 2013  Thomas J Belknap  (email : tbelknap@holisticnetworking.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/**
 * Register an instance of EasyInputs and save it in the global scope.
 * It isn't necessary to do this step in a more focused plugin. But in this
 * case, we do this to make the object available elsewhere.
 */

namespace EasyInputs;

require_once plugin_dir_path(__FILE__) . 'easy-inputs-example.class.php';
$tei    = new EasyInputsExample();

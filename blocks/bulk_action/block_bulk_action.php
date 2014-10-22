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
 * @package   block-custom_reports
 * @copyright 2012 Hina Yousuf
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
	class block_bulk_action extends block_base{
		
		function init(){
			$this->title = get_string('bulk_action', 'block_bulk_action');
			
		}//Function init
		
		function get_content(){
			global $CFG;
			if($this->content !== NULL){
				return $this->content;
			}//if
			$this->content = new stdClass;
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/bulk_action/bulk_actions.php">Bulk Course Action</a></li>';


								
			$this->content->text .= '</ul>';
			return $this->content;
		}//function get_content
	}
?>

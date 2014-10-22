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
 * Auxiliary manual user enrolment lib, the main purpose is to lower memory requirements...
 *
 * @package    enrol
 * @subpackage manual
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/selector/lib.php');

/**
 * Enrol candidates
 */
class enrol_manual_potential_participant extends user_selector_base {
    protected $enrolid;

    public function __construct($name, $options) {
        $this->enrolid  = $options['enrolid'];
        parent::__construct($name, $options);
    }

    /**
     * Candidate users
     * @param <type> $search
     * @return array
     */
public function find_users($search) {
        global $DB;
        
        //@khyam: getting parameters for filtering
        $grp = optional_param('grp', "", PARAM_TEXT);
		$sgrp = optional_param('sgrp', "", PARAM_TEXT);
		//@khyam: end.		
		$sort         = optional_param('sort_', '', PARAM_INT);
   		$course = optional_param('course', "", PARAM_TEXT);
		if(!empty($sort)){
			switch($sort){
				case 1:
					$this->set_extra_fields(array());
					break;
				
				case 2:
					$this->set_extra_fields(array("username"));
					break;
				case 3:
					$this->set_extra_fields(array("email"));
				break;
			}
		}
		$params['sort'] =$sort;
        //by default wherecondition retrieves all users except the deleted, not confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['enrolid'] = $this->enrolid;
        $params['course'] = $course;

       // $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';
        $wherecondition.= " and (defaulter = 0 or defaulter is NULL  )";
        $sql = " FROM {user} u
	                 WHERE $wherecondition";
	   /* if($course!='')
	   		{
	   			 $sql = " FROM {user} u
	                 WHERE $wherecondition
	                      AND u.id NOT IN (SELECT ue.userid
	                                         FROM {user_enrolments} ue
	                                         JOIN {enrol} e ON (e.id = ue.enrolid AND e.id = :course))";
	   			
	   		}else{
	   			$sql = " FROM {user} u
                 WHERE $wherecondition
                      AND u.id NOT IN (SELECT ue.userid
                                         FROM {user_enrolments} ue
                                         JOIN {enrol} e ON (e.id = ue.enrolid AND e.id = :enrolid))";	   			  			
	   		}
	   		*/

        
        //@khyam: group users. 1st level filtering.
        if($grp != ""){
	        $sql .= " AND u.user_group = '{$grp}' ";
	        //@khyam: subgroup users 2nd level filtering.
	        if($sgrp != "")
	        	$sql .= " AND u.user_subgroup = '{$sgrp}' ";
        }
	    //@khyam: end of LDAP filtering.
	    
	//$order = ' ORDER BY u.lastname ASC, u.firstname ASC';
			//$fields      = 'SELECT ' . $this->required_fields_sql('u');
   			 if ($sort==0)
				{
					$order = ' ';
					$fields      = 'SELECT '. $this->required_fields_sql('u');
				}
			if ($sort==1)
				{
				   $order = ' ORDER BY u.lastname ASC, u.firstname ASC';
				   $fields='  SELECT u.firstname,u.lastname ';
				}
   	    	
    		if ($sort==2)
				{
					$order = ' ORDER BY u.username ASC';
					$fields='  SELECT u.username ';
				}
		
		    if ($sort==3)
				{
					$order = ' ORDER BY u.email ASC';
					$fields='  SELECT u.email ';
				}
		//$order = ' ORDER BY :sort';
        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }


        if ($search) {
            $groupname = "Matching Non Fee Defaulter";//get_string('enrolcandidatesmatching', 'enrol', $search);
        } else {
            $groupname ="Non Fee Defaulter"; //get_string('enrolcandidates', 'enrol');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['enrolid'] = $this->enrolid;
        $options['file']    = 'blocks/custom_reports/locallib.php';
        return $options;
    }
}

/**
 * Enroled users
 */
class enrol_manual_current_participant extends user_selector_base {
    protected $courseid;
    protected $enrolid;

    public function __construct($name, $options) {
        $this->enrolid  = $options['enrolid'];
        parent::__construct($name, $options);
    }

    /**
     * Candidate users
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
      /*
    	
    	 global $DB;
        $enrolid      = 7823;//required_param('enrolid', PARAM_INT);
		$role_id      = optional_param('role_id', -1, PARAM_INT);
		//@Hina:getting course context
		$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'manual'), '*', MUST_EXIST);
		$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
		$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
		//@Hina:end
        //by default wherecondition retrieves all users except the deleted, not confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['enrolid'] = $this->enrolid;
   	 	$params['role']    = $role_id;
   	 	$params['contextid'] =$context->id;
   	 	
   	 	//echo $role_id;
		if($role_id>0){
				$wherecondition.= " and u.id IN( SELECT ra.userid FROM {role_assignments} ra WHERE ra.roleid=:role and contextid= :contextid)";
       	}
       //	echo $wherecondition;
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                 JOIN {user_enrolments} ue ON (ue.userid = u.id AND ue.enrolid = :enrolid)
                WHERE $wherecondition";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';
      

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        //print_r($params);
		//echo $fields . $sql . $order;
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);
        
        
     
        if (empty($availableusers)) {
            return array();
        }


        if ($search) {
            $groupname = get_string('enrolledusersmatching', 'enrol', $search);
        } else {
            $groupname = get_string('enrolledusers', 'enrol');
        }

        return array($groupname => $availableusers);*/
    	
    	 
    	
    	 global $DB;
        $enrolid      = 16;//required_param('enrolid', PARAM_INT);
		$role_id      = optional_param('role_id', -1, PARAM_INT);
		//@khyam: getting parameters for filtering
        $grp = optional_param('grp', "", PARAM_TEXT);
                $sgrp = optional_param('sgrp', "", PARAM_TEXT);
                //@khyam: end.  
		//@Hina:getting course context
		$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'manual'), '*', MUST_EXIST);
		$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
		$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
		//@Hina:end
        //by default wherecondition retrieves all users except the deleted, not confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['enrolid'] = $this->enrolid;
   	 	$params['role']    = $role_id;
   	 	$params['contextid'] =$context->id;
   	 	
   	 	//echo $role_id;
		
				$wherecondition.= " and defaulter=1";
       	
       //	echo $wherecondition;
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                
                WHERE $wherecondition";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';
      
	
        //@khyam: group users. 1st level filtering.
        if($grp != ""){
	        $sql .= " AND u.user_group = '{$grp}' ";
	        //@khyam: subgroup users 2nd level filtering.
	        if($sgrp != "")
	        	$sql .= " AND u.user_subgroup = '{$sgrp}' ";
        }
	    //@khyam: end of LDAP filtering.
        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        //print_r($params);
		//echo $fields . $sql . $order;
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);
        
        
     
        if (empty($availableusers)) {
            return array();
        }


        if ($search) {
            $groupname = "Matching Fee Defaulters";
        } else {
            $groupname = "Fee Defaulters";
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['enrolid'] = $this->enrolid;
        $options['file']    = 'blocks/custom_reports/locallib.php';
        return $options;
    }
}

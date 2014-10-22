<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    
    $("select#school").change(function(){
    var id = $("select#school option:selected").attr('value');
    $.post("select_program.php", {id:id}, function(data){
        $("select#program").html(data);
        });
    $.post("get_cohort.php", {id:id}, function(data){
        $("select#cohort").html(data);
        });    
    });
    
    $("select#program").change(function(){
    var id = $("select#program option:selected").attr('value');
    $.post("select_degree.php", {id:id}, function(data){
        $("select#degree").html(data);
        });
    $.post("get_cohort.php", {id:id}, function(data){
        $("select#cohort").html(data);
        });    
    });
    
     $("select#degree").change(function(){
    var id = $("select#degree option:selected").attr('value');
   
    $.post("get_cohort.php", {id:id}, function(data){
        $("select#cohort").html(data);
        });    
    });
    
    $(".button_contact").click(function() { 
    $(".userlist").fadeOut("slow");
  });
    
});
    
</script>
<?php

//  Lists all the users within a given course

    require_once('../../config.php');
    require_once($CFG->libdir.'/tablelib.php');
    require_once($CFG->libdir.'/filelib.php');
    include('get_childcats.php');
  

  // Should use this variable so that we don't break stuff every time a variable is added or changed.
    $baseurl = new moodle_url('/blocks/student_resource_center/student_new_reg2.php'
            );

 
$PAGE->set_context(context_system::instance());
    $PAGE->set_title("Registration");
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('base');
$PAGE->set_heading("Registration");
//$PAGE->navbar->add(get_string('myhome', 'block_student_resource_center'),new moodle_url('/my/'));
$PAGE->navbar->add(get_string('Student_Registration', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/student_new_reg2.php'));

    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    echo '<div class="userlist">';
   
    $opt = new SelectList();
echo '<form method="POST" action="student_new_reg.php" id="select_form" method="POST">';
           echo 'Choose a School:<br />
            <select id="school">';
 echo $opt->ShowSchool() ;
         echo    '</select>
            <br /><br />
               Choose a Program :<br />';
        echo     '<select id="program">
                <option value="0">choose...</option>
            </select>';
         echo   '<br />';
  
    
     echo      ' <br />';
        echo       'Choose a Degree:<br />';
          echo  '<select id="degree">
                <option value="0">choose...</option>
            </select>';
          echo      ' <br /><br />';
          echo       'Choose a Batch:<br />';
             echo  '<select id="batch">
                <option value="0">choose...</option>
            </select>';
          echo '<br /><br />';
        echo       'choose a Cohort:<br />';
        //echo '<option value="" selected="selected"></option>';
          echo  '<select name="cohort" id="cohort">
                <option value="0">choose...</option>
            </select>';
         echo   '<br /><br />';
    
     
         echo   '<input class="button_contact" type="submit" value="confirm" />';
        echo '</form>';
        
    
 
    echo '</div>';
    
// userlist
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();

 
    

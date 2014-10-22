<style>
#d
{
font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
width:100%;
border-collapse:collapse;
}
#d td, #customers th 
{
font-size:1em;
border:1px solid #000033;
text-align:center;
padding:3px 7px 2px 7px;
}
#d th 
{
font-size:1.1em;
text-align:center;
padding-top:5px;
padding-bottom:4px;
background-color:#000033;
color:#ffffff;
}

#d.alt {text-align:left;}
</style>
<?php
require_once('../../../config.php');

require_login();
global  $OUTPUT, $PAGE,$DB,$USER;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/requests/requests_dashboard.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_heading(get_string('requests_dashboard', 'block_student_resource_center'));
$PAGE->navbar->add(get_string('requests_dashboard', 'block_student_resource_center'),$url);
$PAGE->set_title(get_string('requests_dashboard','block_student_resource_center'));
echo $OUTPUT->header();
$sql = "SELECT r.*,rt.request_name FROM {requests} r JOIN {request_types} rt ON rt.id = r.request_typeid  WHERE userid = $USER->id";
$user = $DB->get_records_sql($sql);
echo "<table id ='d'>";
echo "<tr>";
echo '<th width="25">Sr#</th>';
echo '<th width="35">Request ID</th>';
echo '<th width="200">Request Name</th>';
echo "<th width='100'>Status</th>";
echo "<th>Remarks</th>";
echo "</tr>";
$i = 1;
/*
 *  <form method="POST" action="performa.php?reqid=$u->id">
         <input type="hidden" name="reqid" value="<?php echo $u->id;?>">
       <input type="submit" name="download" value="Pro-forma Download">
       </form>
 * <form method="POST" action="cancel_request.php">
        <input type="hidden" name="reqid" value="<?php echo $u->id;?>" >
       <input type="submit" name="cancel" value="Cancel Request">
       </form>
 */
foreach($user AS $u){
   echo "<tr>";
echo "<td width='25'>$i</td>";
echo "<td width='35'>$u->id</td>";
echo "<td width='200' style='text-align:left;'>$u->request_name</td>";
echo "<td width='100'>$u->status</td>";
echo "<td>";
if($u->request_name != 'School Leaving Permission'){
    if($u->status == 'Dismissed'){
       $reason = $DB->get_record_sql("SELECT data FROM {request_info_data} WHERE fieldid = 18 AND requestid = $u->id");
       echo $reason->data;
    }else{
        ?>
            <a href='performa.php?reqid=<?php echo $u->id;?>'><img src='icons/proforma_icon1.png' alt='' border=3 height=30 width=30></img>
           
      <?php if($u->status == 'Applied' || $u->status == 'Pending at Student'){ ?>
                <a href='cancel_request.php?reqid=<?php echo $u->id;?>'><img src='icons/cancel_icon1.png' alt='' border=3 height=30 width=30></img>
        
        <?php
    }
    else { echo "  ";}
 
  
    }
}
else{
   echo $u->status;
    if($u->status == 'Applied' || $u->status == 'Pending at Student'){ ?>
       <a href='cancel_request.php?reqid=<?php echo $u->id;?>'><img src='icons/cancel_icon1.png' alt='' border=3 height=30 width=30></img>
        
    <?php
}
}echo"</td>";
echo "</tr>";
$i = $i + 1;
}
echo "</table>";


echo "<a href='requestslist.php'><img src='icons/dashboard_icon.jpg' alt='' border=3 height=30 width=30></img>Back to Exam Branch Dashboard</a>";
echo $OUTPUT->footer();
      

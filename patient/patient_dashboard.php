<?php
  // check authentication
  session_start();
  if($_SESSION['pat_email']==true){
    include('../connection/db.php');
    $pmail= $_SESSION['pat_email'];
    $query2 =  mysqli_query($conn,"select * from pat_register,pat_login where pat_register.pat_id=pat_login.pat_id and pat_email='$pmail'");
    $row2 = mysqli_fetch_array($query2);
    $pt = $row2['pat_name'];
    $pid = $row2['pat_id'];
  }else{
    header('location:patient_login.php');
  }
?>
<?php
  include('../connection/db.php');
  $query_count_doc =  mysqli_query($conn,"select * from doc_login");
  $query_count_app =  mysqli_query($conn,"select * from appointments where pat_id=$pid and app_date >= CURDATE();");
  $query_app_accept =  mysqli_query($conn,"select * from appointments where pat_id=$pid and app_status='Accepted' and app_date >= CURDATE();");
  $query_app_reject =  mysqli_query($conn,"select * from appointments where pat_id=$pid and app_status='Rejected' and app_date >= CURDATE();");
  $query_app_pending =  mysqli_query($conn,"select * from appointments where pat_id=$pid and app_status='Pending' and app_date >= CURDATE();");
  // number of available doctors
  $doc_count = mysqli_num_rows($query_count_doc);
  // number of appointments to be handle
  $app_count = mysqli_num_rows($query_count_app);
  // number of accepted appointments
  $accept_count = mysqli_num_rows($query_app_accept);
  // number of rejected appointments
  $reject_count = mysqli_num_rows($query_app_reject);
  // number of pending appointments
  $pending_count = mysqli_num_rows($query_app_pending);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('..\widgets\all_links.php'); ?>
<link href="../stylesheet/styleme.css" rel="stylesheet" type="text/css" media="screen, projection"/>

<!-- data table -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
<script>
window.onload = function() {

var chart = new CanvasJS.Chart("chartContainer", {
  backgroundColor: "#464343",
	animationEnabled: true,
	title:{
      text: "Appoinment Status",  //**Change the title here
      fontColor: "#a6d9fc",
      fontFamily:"arial"
      },
  legend: {
		fontSize: 20,
    fontColor: "white"
	},
	data: [{
		type: "pie",
		startAngle: 240,
    showInLegend: true,
    radius: 100,
		yValueFormatString: "##00\"\"",
		indexLabel: "{label} {y}",
    legendText: "{label}",
		dataPoints: [
			{y: <?php echo $pending_count?>, label: "Pending",indexLabelFontColor: "White",indexLabelFontSize:20},
			{y: <?php echo $reject_count?>, label: "Cancel",indexLabelFontColor: "White",indexLabelFontSize:20},
			{y: <?php echo $accept_count?>, label: "Completed",indexLabelFontColor: "White",indexLabelFontSize:20},
			
		]
	}]
});
chart.render();

}
</script>
<style>
   .dataTables_wrapper .dataTables_paginate {
     color:white;
   }
  .column {
  float: left;
  width: 50%;
  padding: 10px;
   
}


.row:after {
  content: "";
  display: table;
  clear: both;
}
* {
  box-sizing: border-box;
}
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
</head>
<body>
<!-- navbar for doctor part -->
<?php include('pat_navbar.php');?>
<!-- vertical navbar for doctors part -->
<?php include('pat_vertical_nav.php');?>

<div class="main">
        <h2 style="color:#a6d9fc" >Patient Dashboard</h2>
  
        <div class="grid-container">
          <div class="row">
              <div class="column">
                  <div style="box-shadow: 0 4px 8px 0 #b1b5b2;" class="grid-item">Available Doctors<br><?php echo $doc_count?></div>
              </div>
              <div class="column">
                  <div style="box-shadow: 0 4px 8px 0 #b1b5b2;" class="grid-item">Appointments<br><?php echo $app_count?></div>
              </div>
			  <div class="row">
			  <hr>
			  <h2 style="color:#a6d9fc"> Your Health Passport ID : <?php echo $pid ?></h2>
			  </div>
          </div>
          <div id="chartContainer" style="height: 200px; width: 200%;"></div> 
        </div>
        <h2 style="color:#a6d9fc" >Upcoming Appointments</h2>
  
        <table style="background-color:black" id="table_id" class="display">
        <thead>
            <tr>
            <th>Doctor Name</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Time</th>
              <th>Location</th>
              <th>Status</th>
              <th>Delete</th>
			  
              
            </tr>
        </thead>
        <tbody>
        <?php
  
            
            $query = mysqli_query($conn,"select * from appointments where pat_id=$pid and app_date = CURDATE() and app_time >= curtime() and app_status !='Rejected'");
            while($row = mysqli_fetch_array($query)) {
        ?>  
          <tr>
              <td><span style="color:black"><?php echo $row['doc_name']?></span></td>
              <td><span style="color:black"><?php echo $row['app_sub']?></span></td>
              <td><span style="color:black"><?php $dt=date_create($row['app_date']); echo date_format($dt,'d/m/Y');?></span></td>
              <td><span style="color:black"><?php $tm = date("g:i a", strtotime($row['app_time'])); echo $tm;?></span></td>
              <td><span style="color:black"><?php echo $row['app_location']?></span></td>
              <td>
                <?php if($row['app_status']=="Pending"){?>
                <span style="color:black">Pending</span>
                <?php }else if($row['app_status']=="Accepted"){?>
                  <div style="color:green" class="tooltip">Accepted
                      <span class="tooltiptext"><?php echo $row['app_msg']?></span>
                  </div>
                <?php }else if($row['app_status']=="Rejected"){?>
                  <div style="color:red" class="tooltip">Rejected
                      <span class="tooltiptext"><?php echo $row['app_msg']?></span>
                  </div>
                <?php }else if($row['app_status']=="Cancelled"){?>
                  <div style="color:red" class="tooltip">Cancelled
                      <span class="tooltiptext"><?php echo $row['app_msg']?></span>
                  </div>

                <?php }?>
              </td>
              <td><a  onclick="confirm_me(<?php echo $row['app_id']; ?>)" ><span style="font-size:25px; color:red" class="fas fa-trash"></span></a></td>
	

          </tr>
          <?php  } ?>
         
        </tbody>
        </table>     

  
</div>

   
</body>
</html>
<script>
function confirm_me(k){
  var r = confirm("Do you really want to delete appointment?");
  if (r == true) {
    window.location.href  = "delete_appointment.php?del="+k+"&&from=dashboard";
  } else {
    window.location.href  = "patient_dashboard.php";
  }
}
</script>
<script>
$(document).ready( function () {
   
    $('#table_id').dataTable( {
          "dom": 'lrtip',
          "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false
        } );
} );
</script>


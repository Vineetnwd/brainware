<?php 
$token = $this->session->userdata('token_id');
$login_id = $token['login_id'];
$user = $this->Offer_model->multitableinfo('user',"id='$login_id'",'user_name');
$center_id = $this->Offer_model->multitableinfo('user',"id='$login_id'",'center_id');
$director = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_director');
$center_name = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_name');
$center_address = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_address');
$center_code = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_code');
$center_mobile = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_mobile');
$center_photo = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_photo');

$this->load->view('admin/header');
$this->load->view('admin/menu');
?>
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Library</a></li> -->
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
            </nav>
            <div class="container-fluid">

                <!--Start Dashboard Content-->

                <div class="row mt-4">
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card bg-primary shadow-primary">
                            <div class="card-body p-4">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-white"><?php  echo $this->Offer_model->dashboard_student($login_id,"status<>'removed'");?></h4>
                                        <span class="text-white">Total Students</span>
                                    </div>
                                    <div class="align-self-center w-icon"><i class="fa fa-users text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card bg-danger shadow-danger">
                            <div class="card-body p-4">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-white"><?php echo $this->Offer_model->dashboard_student($login_id,"comment='pass' and status<>'removed'"); ?></h4>
                                        <span class="text-white">Total Result Out</span>
                                    </div>
                                    <div class="align-self-center w-icon"><i class="fa fa-file-pdf-o text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card bg-success shadow-success">
                            <div class="card-body p-4">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-white"><?php echo $this->Offer_model->dashboard_student($login_id,"comment='' and status<>'removed'");?></h4>
                                        <span class="text-white">Continue Students</span>
                                    </div>
                                    <div class="align-self-center w-icon"><i class="fa text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card bg-warning shadow-warning">
                            <div class="card-body p-4">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-white"><?php echo count($this->Offer_model->select_all('course_details',"status not in('removed','EMPTY')"));?></h4>
                                        <span class="text-white">Total Courses</span>
                                    </div>
                                    <div class="align-self-center w-icon"><i class="fa fa-book text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Row-->

                <div class="row">
                    <div class="col-12 col-lg-7 col-xl-7">
                        <div class="card">
                            <div class="card-header">
                              <span class='text-primary'> Welcome! <?php echo ucwords($user)?></span>
                            </div>
                            <div class="card-body text-center">
                            <div class="user-box">
                                    <img src="<?php echo base_url();?>uploads/<?php echo $center_photo;?>" alt="user avatar"/>
                                </div>
                                <h5 class="mb-1"><?php echo ucwords($director);?></h5>
                                <h6 class="text-muted">Mob. - <?php echo $center_mobile;?></h6>
                                <h6 class="text-muted">Center Code - <?php echo ucwords($center_code);?></h6>
                            
                                <h6 class="text-muted"><?php echo ucwords($center_name);?></h6>
                                <h6 class="text-muted"><?php echo ucwords($center_address);?></h6>
                                <hr>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 col-xl-5">
                        <div class="card">
                            <div class="card-header" style='background-color:#eee'>
                            <center><h3> Date: <?php echo date('d-M-Y');?></h3></center>
                               
                            </div>
                            <div class="card-body">
                            <div class='card-body' style='background-color:#eee'><center>
				<canvas id="canvas" width="250" height="250"
style="background-color:#eee">
</canvas>

<script>
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");
var radius = canvas.height / 2;
ctx.translate(radius, radius);
radius = radius * 0.90
setInterval(drawClock, 1000);

function drawClock() {
  drawFace(ctx, radius);
  drawNumbers(ctx, radius);
  drawTime(ctx, radius);
}

function drawFace(ctx, radius) {
  var grad;
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2*Math.PI);
  ctx.fillStyle = 'white';
  ctx.fill();
  grad = ctx.createRadialGradient(0,0,radius*0.95, 0,0,radius*1.05);
  grad.addColorStop(0, '#333');
  grad.addColorStop(0.5, 'white');
  grad.addColorStop(1, '#333');
  ctx.strokeStyle = grad;
  ctx.lineWidth = radius*0.1;
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(0, 0, radius*0.1, 0, 2*Math.PI);
  ctx.fillStyle = '#333';
  ctx.fill();
}

function drawNumbers(ctx, radius) {
  var ang;
  var num;
  ctx.font = radius*0.15 + "px arial";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  for(num = 1; num < 13; num++){
    ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius){
    var now = new Date();
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();
    //hour
    hour=hour%12;
    hour=(hour*Math.PI/6)+
    (minute*Math.PI/(6*60))+
    (second*Math.PI/(360*60));
    drawHand(ctx, hour, radius*0.5, radius*0.07);
    //minute
    minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
    drawHand(ctx, minute, radius*0.8, radius*0.07);
    // second
    second=(second*Math.PI/30);
    drawHand(ctx, second, radius*0.9, radius*0.02);
}

function drawHand(ctx, pos, length, width) {
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.moveTo(0,0);
    ctx.rotate(pos);
    ctx.lineTo(0, -length);
    ctx.stroke();
    ctx.rotate(-pos);
}
</script>
</center>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Row-->

            </div>
            </div>

                <!--End Dashboard Content-->

            </div>
            <!-- End container-fluid-->

        </div>
        <!--End content-wrapper-->
        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
        <!--End Back To Top Button-->
      <?php $this->load->view('admin/footer');?>
       <!-- Vector map JavaScript -->
    <script src="<?php echo base_url();?>assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- Sparkline JS -->
    <script src="<?php echo base_url();?>assets/plugins/sparkline-charts/jquery.sparkline.min.js"></script>
    <!-- Chart js -->
    <script src="<?php echo base_url();?>assets/plugins/Chart.js/Chart.min.js"></script>
    <!--notification js -->
    <script src="<?php echo base_url();?>assets/plugins/notifications/js/lobibox.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/notifications/js/notifications.min.js"></script>
    <!-- Index js -->
    <script src="<?php echo base_url();?>assets/js/index2.js"></script>
    <script>
        $(window).on('load', function() {
            //loadTable('item', '#tableBody');
        });
    </script>
   
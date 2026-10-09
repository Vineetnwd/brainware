<?php 
if(!$this->session->userdata('token_id'))
{
  redirect(site_url().'logout');
  exit(0);
}
  $token = $this->session->userdata('token_id');
  $login_id = $token['login_id'];
  $user_type= $token['user_type'];
  $login_api = $this->Offer_model->multitableinfo('user',"id='$login_id'",'my_api');

  $strtoken = session_id().$login_id; //.date('ymdhis');
if($login_api<>$strtoken)
{
 redirect(site_url().'logout');
 exit(0);
}
$app = config('app_name');
$page = $this->uri->segment('1');
$page_name = $app." | ".$page;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo config('sc_name');?></title>
    <!--favicon-->
    <link rel="icon" href="<?php echo base_url();?>web/images/favicon.ico" type="image/x-icon">
    <!-- notifications css -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/notifications/css/lobibox.min.css" />
    <!-- Vector CSS -->
    <link href="<?php echo base_url();?>assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- simplebar CSS-->
    <link href="<?php echo base_url();?>assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <!-- Bootstrap core CSS-->
    <link href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- animate CSS-->
    <link href="<?php echo base_url();?>assets/css/animate.css" rel="stylesheet" type="text/css" />
    <!-- Icons CSS-->
    <link href="<?php echo base_url();?>assets/css/icons.css" rel="stylesheet" type="text/css" />
    <!-- Sidebar CSS-->
    <link href="<?php echo base_url();?>assets/css/sidebar-menu.css" rel="stylesheet" />
    <!-- Custom Style-->
    <link href="<?php echo base_url();?>assets/css/app-style.css" rel="stylesheet" />
      <!-- Notifications -->
      <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css"/>
    <!--Data Tables -->
    <link href="<?php echo base_url();?>assets/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url();?>assets/plugins/bootstrap-datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">

</head>

<body>
    <div id='preload-body' class='preload'>
   <center>
   <div class='preload-content' >
     <span class='blink-me'>Please Wait...</span>
    </div>
   </div></center>

    <!-- Start wrapper-->
    <div id="wrapper">
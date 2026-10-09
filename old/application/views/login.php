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
    <!-- Bootstrap core CSS-->
    <link href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- animate CSS-->
    <link href="<?php echo base_url();?>assets/css/animate.css" rel="stylesheet" type="text/css" />
    <!-- Notifications -->
    <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css"/>
     <!-- Icons CSS-->
    <link href="<?php echo base_url();?>assets/css/icons.css" rel="stylesheet" type="text/css" />
    <!-- Custom Style-->
    <link href="<?php echo base_url();?>assets/css/app-style.css" rel="stylesheet" />

</head>
<style>
.card{
    padding-right:10px;
    padding-left:10px;
}
</style>
<body class="login-body">
    <!-- Start wrapper-->
    <div id="wrapper">
        <div class="card card-authentication1 card-default mx-auto my-5 animated bounceInDown">

            <div class="card-body">
                <center> <img src="<?php echo base_url();?>assets/images/brain-ware-logo.png"></center>
                <!-- <h4 class="text-center mt-0 py-2">Mark Jhonsan</h4> -->
                    <div id='loginBody'>
                    <hr class='hr-low'>
                        <form id='loginForm'>
                            <div class="form-group">
                              
                                <label>Registered Mobile No. *</label>
                                <label for="user_mobile" class="sr-only">Enter Password</label>
                                <input type="number" class="form-control " id="user_mobile" name='user_mobile' placeholder="Enter Your Mobile No." maxlength='10' value='' minlength='10' required >
                            </div>
                            <div class="form-group">
                                <label>Password *</label>
                                <label for="user_password" class="sr-only">Enter Password</label>
                                <input type="password" class="form-control " id="user_password" name='user_password' placeholder="Enter Your Password" value='' required>
                            </div>
                            <input type="checkbox" id="md_checkbox_15" class="filled-in chk-col-success" checked />
                            <label for="md_checkbox_15">Remember me</label>
                            <button type="button" class="btn btn-primary btn-sm btn-block waves-effect waves-light mt-2" data-name='user' data-ctrl='login' onClick='login(this)' value='Login'> Login  </button>
                        </form>
                    </div>
                    <div id='forgotBody' style='display: none;'>
                        <!-- <h4 align='center' class='text-danger'>Reset Your Password Here</h4> -->
                        <hr class='hr-low'>
                        <form id='resetForm'>
                            <div class="form-group">
                                <label>Mobile No.</label>
                                <input type="number" class="form-control input-sm" name='user_mobile' id="user_mobile" placeholder="Enter Your Registered Mobile No." minlength='10' maxlength='10' required>
                            </div>
                            <hr>
                            <button type="button" class="btn btn-sm btn-danger btn-block" onClick='resetPass(this)' id='forgotBtn' value='Reset Now'>Reset Now</button>
                        </form>
                    </div>
                    <div class="footer-links">
                        <hr>
                        <div class="col-xs-12 text-center">
                            <span class='text-primary add-pointer' style='display:none' id='loginSpan'><i class="fa fa-arrow-left"></i> Back To Login</span>
                            <span class='text-danger add-pointer' id='forgotSpan'><i class="fa fa-lock"></i> Forgot password</span>
                        </div>
                    </div>
                </div>

                <!--Start Back To Top Button-->
                <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
                <!--End Back To Top Button-->
            </div>
            <!--wrapper-->

            <!-- Bootstrap core JavaScript-->
            <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
            <script src="<?php echo base_url();?>assets/js/popper.min.js"></script>
            <script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
            <!-- waves effect js -->
            <script src="<?php echo base_url();?>assets/js/waves.js"></script>
            <!-- Notifications -->
            <!--notification js -->
            <script src="assets/plugins/notifications/js/lobibox.min.js"></script>
            <script src="assets/plugins/notifications/js/notifications.min.js"></script>
            <script src="assets/plugins/notifications/js/notification-custom-script.js"></script>
            <!-- Custom scripts -->
            <script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
            <script src="<?php echo base_url();?>assets/js/event.js"></script>

</body>

</html>
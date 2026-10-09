 <?php
 if(!$this->session->userdata('token_id'))
 {
//    redirect(site_url().'logout');
//    exit(0);
 }
   $token = $this->session->userdata('token_id');
   $login_id = $token['login_id'];
   $user_type= $token['user_type'];
    $login_api = $this->Offer_model->tableinfo('user',$login_id,'my_api');
 
 $strtoken =session_id().$login_id; //.date('ymdhis');
 if($login_api<>$strtoken)
 {
//   redirect(site_url().'logout');
//   exit(0);
 }
 $app = config('app_name');
 $page = $this->uri->segment('1');
 $page_name = $app." | ".$page; 
 $page = $this->uri->segment(1);
 ?>
 <!--Start sidebar-wrapper-->
        <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
            <div class="brand-logo">
                <a href="<?php echo base_url();?>dashboard">
                    <h5 class="logo-text" align='center'><?php  echo config('app_name');?></h5>
                </a>
            </div>
            <ul class="sidebar-menu do-nicescrol">
            <li ><a href="dashboard" class="waves-effect"><i class="fa fa-home text-red"></i> <span>Dashboard</span></a></li>
            <?php if($user_type=='ADMIN') {?>
            <li ><a href="center" class="waves-effect"><i class="fa fa-bank text-red"></i> <span>Center</span></a></li>
            <li ><a href="user" class="waves-effect"><i class="fa fa-user text-red"></i> <span>Users</span></a></li>
            <li ><a href="module" class="waves-effect"><i class="fa fa-list-alt text-red"></i> <span>Module</span></a></li>
            <li ><a href="course" class="waves-effect"><i class="fa fa-book text-red"></i> <span>Course</span></a></li>
            <?php }?>
            <li ><a href="student" class="waves-effect"><i class="fa fa-child text-red"></i> <span>Students</span></a></li>
            <?php if($user_type=='ADMIN') {?>
            <li ><a href="results" class="waves-effect"><i class="fa fa-file text-red"></i> <span>Results</span></a></li>
            <li ><a href="gallery" class="waves-effect"><i class="fa fa-image text-red"></i> <span>Gallery</span></a></li>
            <li ><a href="query" class="waves-effect"><i class="fa fa-question text-red"></i> <span>Enquiry</span></a></li>
            <?php }?>
            <li data-toggle='modal' data-target='#password-modal'><a href="#change_passord" class="waves-effect" ><i class="fa fa-key text-red"></i> <span>Change Password</span></a></li>
            <li onClick='logOut()'><a href="#" class="waves-effect" ><i class="fa fa-sign-out text-red"></i> <span>Logout</span></a></li>
            </ul>

        </div>
        <!--End sidebar-wrapper-->

        <!--Start topbar header-->
        <header class="topbar-nav">
            <nav class="navbar navbar-expand fixed-top bg-primary">
                <ul class="navbar-nav mr-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link toggle-menu" href="javascript:void();">
                            <i class="icon-menu menu-icon"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <!-- <form class="search-bar">
                            <input type="text" class="form-control" placeholder="Enter keywords">
                            <a href="javascript:void();"><i class="icon-magnifier"></i></a>
                        </form> -->
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center right-nav-link">
                  
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                            <span class="user-profile"><img src="<?php echo base_url();?>uploads/no_image.jpg" class="img-circle" alt="user avatar"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right animated fadeIn">
                            <!-- <li class="dropdown-item user-details">
                                <a href="javaScript:void();">
                                    <div class="media">
                                        <div class="avatar"><img class="align-self-start mr-3" src="<?php echo base_url();?>uploads/no_image.jpg" alt="user avatar"></div>
                                        <div class="media-body">
                                            <h6 class="mt-2 user-title">Katrina Mccoy</h6>
                                            <p class="user-subtitle">katrina92@example.com</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-envelope mr-2"></i> Inbox</li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-wallet mr-2"></i> Account</li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-settings mr-2"></i> Setting</li>
                            <li class="dropdown-divider"></li> -->
                            <li class="dropdown-item" onClick='logOut()' style='cursor:pointer'><i class="icon-power mr-2"></i> Logout</li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>
        <!--End topbar header-->
        <div class="modal fade" id="password-modal">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content animated rollIn">
                      <div class="modal-header">
                        <span class="modal-title text-primary"><i class="fa fa-key"></i> Change Your Password</span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                      <form id='passForm'>
					<div class='form-group'>
						<label>Current Password * </label>
							<input type='password' name='current_password' id='current_password' class='form-control' required>
					</div>
					<div class='form-group'>
						<label>New Password * </label>
							<input type='password' name='new_password' onKeyUp='confirmPass()' id='new_password' class='form-control' required minlength='8' maxlength='16'>
					</div>
					<div class='form-group'>
						<label>Confirm New Password * &nbsp; &nbsp; <i class='fa fa-eye' title='Check Password' style='color:#b30581;cursor:pointer' onClick='passToggle()'></i></label>
							<input type='password' onKeyUp='confirmPass()' name='confirm_password' id='confirm_password' class='form-control' required minlength='8' maxlength='16'>
					</div>
					<span id='passError' style='font-size:10px;color:red'></span>
                </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs" onClick='changePassword(this)' data-ctrl='change_password' data-name='user' data-frm='#passForm' value='Change Now'><i class="fa fa-check-square-o"></i> Change Now</button>
                </div>
            </div>
            </div>
        </div>
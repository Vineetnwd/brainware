<?php 
$this->load->view('admin/header');
$this->load->view('admin/menu');
?>
        <!--End topbar header-->

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                       
                            <!-- <div class="card-header"><i class="fa fa-table"></i> Data Table Example</div> -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="default-datatable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan='8' class='text-center'>
                                                <button type="button" class="btn btn-success btn-xs  waves-effect waves-light mt-2" style='float:left' onClick='toggleCheck()'>  <input type="checkbox" id='masterCheckBox' onClick='checkIdAll(this)'>&nbsp; &nbsp; All</button>
                                                <!-- <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='user' data-toggle='modal' data-target='#user-modal' onmouseover="makeDefault(this)" data-modal='center'>New</button> -->
                                               &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='user'>Edit</button>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='user'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Center Code</th>
                                                <!--<th>Name</th>-->
                                                <th>Mobile No</th>
                                                <th>Password</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Row-->
            </div>
            <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
        </div>
        <!--End wrapper-->
        <div class="modal fade" id="user-modal">
        <div class="modal-dialog">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='userTitleNew'></span>
            <span class="modal-title text-warning" id='userTitleUpdate' style='display:none'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='userForm'>
                <div class='row'>
                 <div class="col-md-12">
                   
                        <div class="form-group">
                            <label>Enter Center Code *</label>
                            <input class="form-control" id='user_name' placeholder="BR/CPR/001" name='user_name' autofocus required>
                        </div>
                    <div class="form-group">
                            <label>Enter Mobile No. (Center/ Director) *</label>
                            <input class="form-control" type='number' placeholder="" id='user_mobile' name='user_mobile' maxlength='10' minlength='10' required>
                        </div>
                        <div class="form-group">
                            <label>Enter Email Id. (Center/ Director) </label>
                            <input class="form-control" placeholder="someone@email.com" id='user_email' name='user_email' type='email' >
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input class="form-control" placeholder="" id='user_password' name='user_password' type='text' >
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select name='status' id='status' class='form-control'>
                                <option value=''>----Select One----</option>
                                <?php 
                                $status_arr = dropdown('general_status');
                                foreach($status_arr as $st)
                                {
                                ?>
                                <option value='<?php echo $st;?>'><?php echo $st;?></option>
                                <?php }?>
                            </select>
                        </div>
                    
                 </div>
              </div>
              </form>
            <input type='hidden' id='id'>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='user' data-frm='#userForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
  
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('user', '#default-datatable');
        });
    </script>
   

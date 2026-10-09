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
                    <li class="breadcrumb-item active" aria-current="page">Center</li>
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
                                                <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='center_details' data-toggle='modal' data-target='#center-modal' onmouseover="makeDefault(this)" data-modal='center'>New</button>
                                               &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='center'>Edit</button>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='center_details'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Center_Code</th>
                                                <th>Name</th>
                                                <th>Director's Name</th>
                                                <th>Mobile_No</th>
                                                <th>Address</th>
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
        <div class="modal fade" id="center-modal">
        <div class="modal-dialog">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='centerTitleNew'>Add New Center</span>
            <span class="modal-title text-warning" id='centerTitleUpdate' style='display:none'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='centerForm'>
                <div class='row'>
                 <div class="col-md-6">
                   
                        <div class="form-group">
                            <label>Enter Center Code *</label>
                            <input class="form-control" id='center_code' placeholder="BR/CPR/001" name='center_code' data-error='#errorMsg' data-name='center_details' onKeyUp='checkDuplicate(this)' autofocus required>
                            <span class='error' id='errorMsg'></span>
                         </div>	
                        <div class="form-group">
                            <label>Enter Center Name *</label>
                            <input class="form-control" placeholder="Full Name of Center" id='center_name' name='center_name' required>
                        </div>											
                        <div class="form-group">
                            <label>Enter Director Name *</label>
                            <input class="form-control" placeholder="Director Name Here" id='center_director' name='center_director' required>
                        </div>
                        
                
                        <div class="form-group">
                            <label>Enter Father's Name *</label>
                            <input class="form-control" placeholder="Father's Name" name='center_father' id='center_father' required>
                        </div>
                        <div class="form-group">
                            <label>Date of Starting *</label>
                            <input class="form-control"  type='date' id='center_dos' name='center_dos' value='<?php echo date('Y-m-d');?>' >
                        </div>
					</div>
                    <div class="col-md-6">
                    <div class="form-group">
                            <label>Enter Mobile No. (Center/ Director) *</label>
                            <input class="form-control" type='number' placeholder="" id='center_mobile' name='center_mobile' maxlength='10' minlength='10' required>
                        </div>
                        <div class="form-group">
                            <label>Enter Email Id. (Center/ Director) </label>
                            <input class="form-control" placeholder="someone@email.com" id='center_email' name='center_email' type='email' >
                        </div>
                        <div class="form-group">
                            <label>Address (center) *</label>
                            <textarea class="form-control" rows="3" id='center_address' name='center_address'></textarea>
                        </div>
                        <div class="form-group">
                        <label>Upload Photograph </label>
                        <input type="file" id='center_photo' class='form-control' accept='image/*' data-name='center_details' data-category='photo' display-tag='#dispPhoto'>
                    </div>
                        <center><img  id='dispPhoto' src='#' height='50px' style='display:none'></span></center>
                
                       <input type='hidden' name='status' value='ACTIVE'>
                    
                 </div>
              </div>
              </form>
            <input type='hidden' id='id'>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='save_center' data-name='center_details' data-frm='#centerForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
  
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('center_details', '#default-datatable');
        });
    </script>
   

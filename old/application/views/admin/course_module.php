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
                    <li class="breadcrumb-item active" aria-current="page">Course Module</li>
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
                                                <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='module' data-toggle='modal' data-target='#module-modal' onmouseover="makeDefault(this)" data-modal='module'>New</button>
                                               &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='module'>Edit</button>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='module'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Module Name</th>
                                                <th>Semester</th>
                                                <th>Syllabus</th>
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
        <div class="modal fade" id="module-modal">
        <div class="modal-dialog">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='moduleTitleNew'>Add New Module</span>
            <span class="modal-title text-warning" id='moduleTitleUpdate' style='display:none'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='moduleForm'>
                <div class='row'>
                 <div class="col-md-12">
                   
                    <div class="form-group">
                            <label>Module Name *</label>
                            <input class="form-control" type='text' placeholder="" id='module_name' name='module_name' required>
                        </div>
                        <div class="form-group">
                            <label>Semester No. *</label>
                            <input class="form-control" placeholder="" id='module_semester' name='module_semester' type='number' required >
                        </div>
                        <div class="form-group">
                            <label>Syllabus *</label>
                            <textarea class="form-control" placeholder="Type Syllabus With Comma Seperated" rows='5' id='module_syllabus' name='module_syllabus' required></textarea>
                        </div>
                        <input type='hidden' name='status' value='ACTIVE'>
                        <!-- <div class="form-group">
                            <label>Status *</label>
                            <select name='status' id='status' class='form-control'>
                                <option value=''>----Select One----</option>
                                <?php 
                                $status_arr = dropdown('status');
                                foreach($status_arr as $st)
                                {
                                ?>
                                <option value='<?php echo $st;?>'><?php echo $st;?></option>
                                <?php }?>
                            </select>
                        </div> -->
                    
                 </div>
              </div>
              </form>
            <input type='hidden' id='id'>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='module' data-frm='#moduleForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
  
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('module', '#default-datatable');
        });
    </script>
   

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
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
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
                                                <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='gallery' data-toggle='modal' data-target='#gallery-modal' onmouseover="makeDefault(this)" data-modal='gallery'>New</button>
                                               <!-- &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='gallery'>Edit</button> -->
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='gallery'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Image Title</th>
                                                <th>Date </th>
                                                <th>Discription</th>
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
        <div class="modal fade" id="gallery-modal">
        <div class="modal-dialog modal-sm">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='galleryTitleNew'>Add New Gallery Image</span>
            <!-- <span class="modal-title text-warning" id='userTitleUpdate' style='display:none'></span> -->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='galleryForm'>
                <div class='row'>
                 <div class="col-md-12">
                   
                        <div class="form-group">
                            <label>Image Title *</label>
                            <input class="form-control" id='image_title' name='image_title' autofocus required>
                        </div>
                    <div class="form-group">
                            <label>Date *</label>
                            <input class="form-control" type='date' placeholder="" id='image_date' name='image_date' value='<?php echo date('Y-m-d');?>'>
                        </div>
                        <div class='form-group'>
                      <label>Image Description </label>
                      <textarea anme='image_description' name='image_description' class='form-control'></textarea>
                      </div>
                        <div class="form-group">
                            <label>Upload Image </label>
                            <input type="file" id='image_url' class='form-control' accept='image/*' data-name='gallery' data-category='photo' display-tag='#dispPhoto' capture='camera'>
                        </div>
                            <center><img  id='dispPhoto' src='#' height='50px' style='display:none'></span></center>
                    
                    <input type='hidden' name='status' value='ACTIVE'>
                 </div>
              </div>
              </form>
              <input type='hidden' id='id'>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='gallery' data-frm='#galleryForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
  
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('gallery', '#default-datatable');
        });
    </script>
   

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
                    <li class="breadcrumb-item active" aria-current="page">Course</li>
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
                                                <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='course_details' data-toggle='modal' data-target='#course-modal' onmouseover="makeDefault(this)" data-modal='course'>New</button>
                                               &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='course'>Edit</button>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='course_details'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Course_Code</th>
                                                <th>Course Name</th>
                                                <th>Duration</th>
                                                <th>Semester</th>
                                                <th>Syllabus</th>
                                                <!-- <th>Status</th> -->
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
        <div class="modal fade" id="course-modal">
        <div class="modal-dialog">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='courseTitleNew'>Add New Course Here</span>
            <span class="modal-title text-warning" id='courseTitleUpdate' style='display:none'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='courseForm'>
                <div class='row'>
                 <div class="col-md-12">
                   
                    <div class="form-group">
                        <label>Enter Course Code </label>
                        <input class="form-control"  name='course_code' id='course_code' autofocus required>
                    </div>	
                    <div class="form-group">
                        <label>Enter Course Name /Title </label>
                        <input class="form-control" name='course_name' id='course_name' required>
                    </div>											
                    <div class="form-group">
                        <label> Course Duration </label>
                        <input class="form-control"  name='course_duration' id='course_duration' required>
                    </div>
                    <div class="form-group">
                        <label> No Of Semester </label>
                        <input type ='number' class="form-control"  name='no_of_semester' id='no_of_semester' required>
                    </div>
                    <input type='hidden' name='status' value='ACTIVE'>
                    <input type='hidden' name='course_syllabus' id='course_syllabus'>
                    </div>
                    </div>
                    </form>
                        Select Module *
                       <div style='width:100%;border:solid 1px #ccc; height:150px; max-height:200px;padding:5px;overflow:auto'>
                        <?php 
                        $module_arr = $this->Offer_model->getarray('module',"status in('ACTIVE')");
                        foreach($module_arr as $module)
                        {
                            ?>
                            &nbsp; <input onChange='fillSyllabus(this)' value='<?php echo $module['id'];?>' type='checkbox' class='module-id'  id='module<?php echo $module['id'];?>' style='position:relative;height:15px'> &nbsp; &nbsp;  <?php echo $module['module_name'];?> &nbsp; (Semester <?php echo $module['module_semester'];?>) <hr style='margin:0px'>
                            <?php 
                        }
                        ?>
              </div>
             
            <input type='hidden' id='id'>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='course_details' data-frm='#courseForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
  
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('course_details', '#default-datatable');
        });
    </script>
   

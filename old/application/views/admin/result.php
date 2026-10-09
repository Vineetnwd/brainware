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
                    <li class="breadcrumb-item active" aria-current="page">Results</li>
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
                                                <th colspan='10' class='text-center'>
                                                <button type="button" class="btn btn-success btn-xs  waves-effect waves-light mt-2" style='float:left' onClick='toggleCheck()'>  <input type="checkbox" id='masterCheckBox' onClick='checkIdAll(this)'>&nbsp; &nbsp; All</button>
                                                <!-- <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='center_details' data-toggle='modal' data-target='#center-modal' onmouseover="makeDefault(this)" data-modal='center'>New</button> -->
                                               &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editResult(this)' data-name='center'>Edit</button>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='result'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>   <!--Checkbox table heading-->
                                                <!-- <th>Reg. No.</th> -->
                                                <th>Student's Name</th>
                                                <!-- <th>Course</th> -->
                                                <th>Exam.</th>
                                                <th>Written</th>
                                                <th>Lab</th>
                                                <th>Assignment</th>
                                                <th>Viva</th>
                                                <th>Marksheet No.</th>
                                                <th>Date </th>
                                                <th>Remarks </th>
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
        <div class="modal fade" id="result-modal">
        <div class="modal-dialog modal-lg">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-warning" id='resultTitleUpdate'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='resultForm'>
                <div class='row'>
                        <div class='col-lg-6'>
                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name='exam_name' id='exam_name'  onChange='fillMarks(this)' required>
                                    <option value=''>Select Exam Name</option>
                                    <option value='semester 1'>Semester 1</option>
                                    <option value='semester 2'>Semester 2</option>
                                    <option value='semester 3'>Semester 3</option>
                                    <option value='semester 4'>Semester 4</option>
                                </select>
                            </div>
                            
                                                                    
                            <div class="form-group">
                                <label> Written  </label>
                                <input class="form-control"  name='written'  type='number' id='written' minlength='0' maxlength='100' required>
                            </div>
                            
                            <div class="form-group">
                                <label> Practical / Lab </label>
                                <input  class="form-control" name='practical'  type='number' id='practical'  maxlength='100' minlength='0' required>
                            </div>
                            <div class="form-group">
                                <label> Assignment / Project </label>
                                <input  class="form-control" name='assignment'  maxlength='100' minlength='0' type='number' id='assignment'  required>
                            </div>
                           
            </div>	
            <div class="col-md-6">				
                         <div class="form-group">
                                <label> Viva / Interview  </label>
                                <input class="form-control"  name='viva'  maxlength='100' minlength='0'  type='number' id='viva' required>
                            </div>
                            
                            <div class="form-group">
                                <label> Remarks (PASS/ FAIL)  </label>
                                <input class="form-control"  name='remarks'  type='text' id='remarks' required>
                            </div>
                            
                            <div class="form-group">
                                <label> Serial Number  </label>
                                <input class="form-control"  name='ms_no'  type='text' id='ms_no' id='ms_no' required>
                                <div id='result'> </div>
                            </div>
                            <div class="form-group">
                                <label>Date of Certificate Issue</label>
                                <input class="form-control"  type='date' name='date_of_issue' id='date_of_issue' >
                            </div>
                       </form>  
                       <input type='hidden' id='id'>   
            </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='result' data-frm='#resultForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('result', '#default-datatable');
        });
    </script>
   

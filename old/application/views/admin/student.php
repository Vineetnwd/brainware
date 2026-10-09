<?php 
$this->load->view('admin/header');
$this->load->view('admin/menu');
$token = $this->session->userdata('token_id');
$login_id = $token['login_id'];
$user_type= $token['user_type'];
$center_id = $this->Offer_model->multitableinfo('user',"id='$login_id'","center_id");
$center_code = $this->Offer_model->multitableinfo('center_details',"id='$center_id'","center_code");
?>
        <!--End topbar header-->

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
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
                                                <button type="button" class="btn btn-primary btn-xs  waves-effect waves-light mt-2" onClick='generateNew(this)' data-name='student' data-toggle='modal' data-target='#student-modal' onmouseover="makeDefault(this)" data-modal='student'>New</button>
                                                &nbsp;<button type="button" class="btn btn-warning btn-xs  waves-effect waves-light mt-2" onClick='editNow(this)' data-name='student'>Edit</button>
                                                <?php if($user_type=='ADMIN'){?>
                                                &nbsp; <button type="button" class="btn btn-info btn-xs  waves-effect waves-light mt-2"  onClick='updateResult(this)' onmouseover="cleanModal()"  value='Marks' data-modal='marks'>Marks</button>
                                                &nbsp;<button type="button" class="btn btn-dark btn-xs  waves-effect waves-light mt-2" onClick='sessionGen(this)' data-location='student_certificate'  data-id_name='result_dataId' data-name='result_session' value='Certificate'>Certificate</button>

                                                <!-- &nbsp; <button type="button" class="btn btn-dark btn-xs  waves-effect waves-light mt-2"  onClick='updateResult(this)' value='Certificate' data-modal='certificate'>Certificate</button> -->
                                                <?php }?>
                                                &nbsp; <button type="button" class="btn btn-danger btn-xs  waves-effect waves-light mt-2" data-ctrl='delete' onClick='deleteData(this)' value='Delete' data-name='student'>Delete</button>

                                                </th>
                                            <tr>
                                                <th></th>
                                                <th>Study Center</th>
                                                <th>Course Code</th>
                                                <th>Reg. No.</th>
                                                <th>Student Name</th>
                                                <th>Mobile NO.</th>
                                                <th>Cert. NO.</th>
                                                <th>Grade</th>
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
        <div class="modal fade" id="student-modal">
        <div class="modal-dialog modal-lg">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-primary" id='studentTitleNew'>Add New Student Here</span>
            <span class="modal-title text-warning" id='studentTitleUpdate' style='display:none'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='studentForm'>
                <div class='row'>
                <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select Center Code </label>
											 <select class="form-control" name='center_id' id='center_id' onChange='fillRegNo()' required>
                                               <option value=''>---- Select One ----</option>
                                               <?php 
                                               if($user_type=='ADMIN')
                                               {
                                                    $cent_list =  $this->Offer_model->getarray('center_details',"status not in('removed','EMPTY')");
                                                    foreach($cent_list as $center)
                                                    {
                                                            echo "<option value='$center[id]'>".$center['center_code']."</option>";
                                                    }
                                                }
                                                else 
                                                {
                                                    echo "<option value='$center_id' selected>$center_code</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>	
										<div class="form-group">
                                            <label>Select Course Name </label>
                                            <select class="form-control" name='course_id' id='course_id' required>
                                            <option value=''>---- Select One ----</option>
                                               <?php 
                                               $course_list =  $this->Offer_model->select_all('course_details',"status not in('removed','EMPTY')");
                                               foreach($course_list as $course)
                                               {
                                                    echo "<option value='$course->id'>".$course->course_code."</option>";
                                               }
                                                ?>
                                            </select>
											<info style='color:red'> </info>
                                        </div>	
										<div class="form-group">
                                            <label> Reg. No.</label>
                                            <input class="form-control" placeholder="Reg No Here" id='student_roll' onKeyUp='fillRegNo()' data-error='#errorMsg' onBlur='checkDuplicate(this)' data-column='registration_no' data-name='student' name='student_roll' required>
                                               <span class='error' id='errorMsg'></span>
                                      
                                        </div>
										<div class="form-group">
                                            <label>Enter Student Name</label>
                                            <input class="form-control" placeholder="Student Name Here" id='student_name' name='student_name' required>
                                        </div>
                                          
										<div class="form-group">
                                            <label>Enter Father's Name</label>
                                            <input class="form-control" placeholder="Father's Name" id='student_father' name='student_father' required>
                                        </div>
										
										
					</div>	
					<div class="col-md-4">
                                      
										<div class="form-group">
                                            <label>Enter Mother's Name</label>
                                            <input class="form-control" placeholder="Mother's Name" id='student_mother' name='student_mother'>
                                        </div>
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input class="form-control"  type='date' name='date_of_birth' id='date_of_birth' max='2015-01-01' required>
                                        </div>
						
										<div class="form-group">
                                            <label>Select Sex</label>
                                            <select class="form-control" name='student_sex' id='student_sex' required>
                                                <option value=''>Select A Value</option>
                                                <option value='MALE'>MALE</option>
                                                <option value='FEMALE'>FEMALE</option>
                                            </select>
                                        </div>
                                        	
										<div class="form-group">
                                            <label>Enter Mobile No.  </label>
                                            <input type='number' class="form-control" placeholder="" id='student_mobile' name='student_mobile' maxlength='10' minlength='10' required>
                                        </div>
                                        <div class="form-group">
                                            <label>Enter Email Id. </label>
                                            <input class="form-control" placeholder="someone@email.com" id='student_email' name='student_email' type='email' >
                                        </div>
						</div>	
						<div class="col-md-4">
										<div class="form-group">
                                            <label>Address </label>
                                            <textarea class="form-control" rows="3" id='student_address' name='student_address'></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Blood Group</label>
                                            <select class="form-control" name='student_bloodgroup' id='student_bloodgroup'>
                                                <option value='NA'>Not Available </option>
                                                <option value='A+'>A +</option>
                                                <option value='AB+'>AB +</option>
                                                <option value='B+'>B +</option>
                                                <option value='O+'> O +</option>
												  <option value='A-'>A - </option>
                                                <option value='AB-'>AB -</option>
                                                <option value='B-'>B -</option>
                                                <option value='O-'> O -</option>
                                                
                                            </select>
                                        </div>
                                        <div>
                                        <input type='hidden' id='registration_no' name='registration_no' value='' readonly>
                                        <input type='hidden' name='status' value='ACTIVE'>
                                        <div class="form-group">
                                            <label>Upload Photograph </label>
                                            <input type="file" id='student_photo' class='form-control' accept='image/*' data-name='student' data-category='photo' display-tag='#dispPhoto' capture='camera'>
                                        </div>
                                            <center><img  id='dispPhoto' src='#' height='50px' style='display:none'></span></center>
                                        </div>
                                     
              </form>
            
            </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='student' data-frm='#studentForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
    <!-- ---------------------input id -------------------------- -->
    <input type='hidden' id='id'>  
  <!-- -----------------------------------------Modal Dialog 2------------------------------------ -->
  <div class="modal fade" id="marks-modal">
        <div class="modal-dialog modal-lg">
        <div class="modal-content animated zoomInUp">
            <div class="modal-header">
            <span class="modal-title text-warning" id='marksTitleUpdate'></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form id='marksForm'>
                <div class='row'>
                        <div class='col-lg-6'>
                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name='exam_name'   onChange='fillMarks(this)' required>
                                    <option value=''>Select Exam Name</option>
                                    <!-- <option value='semester 1'>Semester 1</option>
                                    <option value='semester 2'>Semester 2</option>
                                    <option value='semester 3'>Semester 3</option>
                                    <option value='semester 4'>Semester 4</option> -->
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
                                <input class="form-control"  name='ms_no' data-error='#msError' onKeyUp='checkDuplicate(this)' type='text' data-name='result' id='ms_no' id='ms_no' required>
                                <span id='msError' class='error'></span>
                          
                            </div>
                            <div class="form-group">
                                <label>Date of Certificate Issue</label>
                                <input class="form-control"  type='date' name='date_of_issue' id='date_of_issue' >
                            </div>
                            <input type='hidden' name='status' value='ACTIVE'>
                            <div style='display:none'>
                            <input type='text' runat='server' name='student_id' id='student_id'>
                       </form>  
                           
            </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-xs" onClick='updateMarks(this)' data-ctrl='update_me' data-name='result' data-frm='#marksForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button>
            </div>
        </div>
        </div>
    </div>
    <!-- -----------------------------------------------------certificate Modal------------------------ -->
    
    <?php 
    $this->load->view('admin/footer');
    ?>
     <script>
        $(window).on('load', function() {
            loadTable('student', '#default-datatable');
        });
    </script>
   

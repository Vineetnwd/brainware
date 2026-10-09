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
                    <li class="breadcrumb-item active" aria-current="page">Certificate Entry</li>
                </ol>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                       
                            <!-- <div class="card-header"><i class="fa fa-table"></i> Data Table Example</div> -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php 
                                    $student_data = $_SESSION['result_session'];
                                    $student_id = $student_data['result_dataId'];
                                    $result_data  = $this->Offer_model->getlimitarray("result","student_id='$student_id' and status in('ACTIVE')");
                                  
                                        $student_name = $this->Offer_model->multitableinfo('student',"id='$student_id'",'student_name');
                                        $reg = $this->Offer_model->multitableinfo('student',"id='$student_id'",'registration_no');
                                        $cert = $this->Offer_model->multitableinfo('student',"id='$student_id'",'certificate_no');
                                        $doi = $this->Offer_model->multitableinfo('student',"id='$student_id'",'date_of_issue');
                                        $percent_age = $this->Offer_model->multitableinfo('student',"id='$student_id'",'finalpercentage');
                                        $comment = $this->Offer_model->multitableinfo('student',"id='$student_id'",'comment');
                                        $grade = $this->Offer_model->multitableinfo('student',"id='$student_id'",'grade');
                                        $center_id = $this->Offer_model->multitableinfo('student',"id='$student_id'",'center_id');
                                        $course_id = $this->Offer_model->multitableinfo('student',"id='$student_id'",'course_id');
                                        $center_name = $this->Offer_model->multitableinfo('center_details',"id='$center_id'",'center_name');
                                        $course_code = $this->Offer_model->multitableinfo('course_details',"id='$course_id'",'course_code');
                                        $semester = $this->Offer_model->multitableinfo('course_details',"id='$course_id'",'no_of_semester');
                                    ?>
                                </div>
                                <h5 align='center'>Certificate Details Of <span class='text-warning'><?php echo $student_name?></span></h4>
                                <table class='table table-bordered table-hover'>
                                    <thead>
                                        <th>Exam</th>
                                        <th>Written</th>
                                        <th>Lab</th>
                                        <th>Assignment</th>
                                        <th>Viva</th>
                                        <th>Total</th>
                                        <th>Percentage (%)</th>
                                        <th>M.S. No.</th>
                                    </thead>
                                    <tbody>
                                        <?php 
                                       $tw=0;
                                       $tl=0;
                                       $ta=0;
                                       $tv=0;
                                       $i=0;
                                        $grand_total=0;
                                       
                                        foreach($result_data as $res_row)
                                        {
                                           $total=0;
                                            $written =  $res_row['written'];
                                            $lab =  $res_row['practical'];
                                            $assign =  $res_row['assignment'];
                                            $viva =  $res_row['viva'];
                                            $total = $written+$lab+$assign+$viva;
                                           
                                            $tw = $tw+$written;
                                            $tl = $tl+$lab;
                                            $ta = $ta+$assign;
                                            $tv = $tv+$viva;
                                            $grand_total = $total+$grand_total;
                                            $i++;
                                            ?>
                                                <tr>
                                                    <td><?php echo $res_row['exam_name'];?></td>
                                                    <td><?php echo $written; ?></td>
                                                    <td><?php echo $lab; ?></td>
                                                    <td><?php echo $assign; ?></td>
                                                    <td><?php echo  $viva; ?></td>
                                                    <td><?php echo $total; ?></td>
                                                    <td><?php echo ($total/4); ?></td>
                                                    <td><?php echo $res_row['ms_no']; ?></td>
                                                </tr>
                                                
                                            <?php 
                                        }
                                        ?>
                                        <tr>
                                            <th>Total</th>
                                            <th><?php echo $tw;?></th>
                                            <th><?php echo $tl;?></th>
                                            <th><?php echo $ta;?></th>
                                            <th><?php echo $tv;?></th>
                                            <th><?php echo $grand_total+0;?></th>
                                            <th class='text-danger text-center'><?php echo $pm = round((($grand_total)/($semester*4)),2);?></th>
                                            <th class='text-danger text-center'>Grade : <?php echo  gradeshow($pm); ;?></th>
                                        </tr>
                                    </tbody>
                                </table>
                               
                                <h6 align='center' class='text-warning'>Enter Certificate Details</h6>
                                <hr>
                                <form id='certificateForm'>
                                <div class='row'>
                                    <div class='col-lg-4'>
                                        <div class='form-group'>
                                            <label>Student's Name </label>
                                            <input type='text' value='<?php echo $student_name;?>' class='form-control' disabled>
                                        </div>
                                        <div class='form-group'>
                                            <label>Resgistration No. </label>
                                            <input type='text' value='<?php echo $reg;?>' class='form-control' disabled>
                                        </div>
                                        <div class='form-group'>
                                            <label>Study Center </label>
                                            <input type='text' value='<?php echo $center_name;?>' class='form-control' disabled>
                                        </div>
                                    </div>
                                    <div class='col-lg-4'>
                                    <div class='form-group'>
                                            <label>Course </label>
                                            <input type='text' value='<?php echo $course_code;?>' class='form-control' disabled>
                                        </div>
                                  
                                                    <div class="form-group">
                                                                        <label>Enter Certificate No</label>
                                                                        <input class="form-control" value='<?php echo $cert;?>' name='certificate_no' id='certificate_no' data-error='#certError' data-name='student' onKeyUp='checkDuplicate(this)' autofocus required >
                                                                        <span id='certError' class='error'></span>
                                                                
                                                                    </div>
                                                    <div class="form-group">
                                                        <label>Enter Grade </label>
                                                        <input class="form-control" value='<?php echo $grade;?>' name='grade' id='grade' required>
                                                    </div>
                                        </div>                
                                        <div class='col-lg-4'>                
                                                    <div class="form-group">
                                                        <label>Date of Issue</label>
                                                        <input class="form-control" value='<?php echo $doi;?>' type='date' name='date_of_issue' id='date_of_issue' required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label> Percentage </label>
                                                        <input class="form-control" value='<?php echo $percent_age;?>' name='finalpercentage' id='finalpercentage' required >
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Comment (Pass/ Fail) </label>
                                                        <input class="form-control" name='comment' value='<?php echo $comment;?>' id='comment' required>
                                                    </div>
                                                    <!-- <input type='text' name='status' value='ACTIVE'> -->
                                                </form>
                                        </div>
                                        <input type='hidden' id='id' value='<?php echo $student_id;?>'>
                                       
                                        <div class='col-lg-12'>
                                            <button type="button" class="btn btn-primary btn-block btn-xs" onClick='updateData(this)' data-ctrl='update_me' data-name='student' data-frm='#certificateForm' value='Save Now'><i class="fa fa-check-square-o"></i> Save Now</button></center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Row-->
            </div>
            <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
        </div>
       
    <?php 
    $this->load->view('admin/footer');
    ?>
     
   

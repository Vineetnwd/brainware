
		<!-- CONTENT -->
		<div id="page-content">
			
			<div id="page-header" class="parallax" data-stellar-background-ratio="0.1" style="background-image:url(<?php echo base_url();?>web/images/backgrounds/page-header-1.jpg);">
				
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							
							<h3>Result</h3>
							
						</div><!-- col -->
					</div><!-- row -->
				</div><!-- container -->    
				
				<a class="go-to-section" href="#go-to-section"><i class="fa fa-angle-down"></i></a>
				
			</div><!-- page-header -->
			
			<div id="go-to-section"></div>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
					
						<div class="headlines">
								
							<!-- <h2><span class='text-primary'> </span><span class='text-warning'>Result </span></h2> -->
							<h2 align='center'><span class='text-primary'> </span><span class='text-warning'>Get Your Result Here</span></h2><br>
						
						<?php 
							
							if(isset($_POST['search']))
							{
								extract($_POST);
								$center_id = $this->Offer_model->multitableinfo('center_details',"center_code='$center_code'","id");
								
								$row = $this->Offer_model->getarray('student',"student_roll='$student_roll' and center_id='$center_id'  and status<>'removed'")[0];
							if($row)
							{
								$student_id = $row['id'];
							$result = $this->Offer_model->getarray('result',"student_id='$student_id' and exam_name='$exam_name'");
								if($result)
								{
							?>
								<div class='row'>
							<div class='col-lg-2'></div>
							<div class='col-lg-8'>
							<div class='table-responsive'>
								<center>
								<table class='table table-bordered' style='margin-bottom:-1px'>
									<tr style='background: #666;color:#fff;font-size:14px;font-weight:900'>
										<th colspan='4'><center>Result Details</center></th>
									</tr>
									<tr>
										<th>Registration No.</th>
										<td><?php echo $row['registration_no']?></td>
										<td colspan='2' align='center' rowspan='4' style='width:115px'><img src='<?php echo base_url();?>uploads/<?php echo $row['student_photo'];?>' class='img-thumbnail' width='120px'></td>
									</tr>
									<tr>
										<th>Name Of Center</th>
										<td><?php echo $this->Offer_model->multitableinfo('center_details',"id=".$row['center_id'],"center_name");?></td>
										
									<tr>
									<tr>
										<th>Name</th>
										<td><?php echo $row['student_name'];?></td>
										
									</tr>
									<tr>
										<th>Father's Name</th>
										<td><?php echo $row['student_father'];?></td>
										<th>Mother's Name</th>
										<td><?php echo $row['student_mother'];?></td>
									</tr>
									<!-- <tr>
										<th>Mother's Name</th>
										<td><?php echo $row['student_mother'];?></td>
									<tr> -->
									<tr>
										<th>Date Of Birth</th>
										<td><?php echo nice_date($row['date_of_birth'],'d-M-Y');?></td>
										<th>Gender</th>
										<td><?php echo $row['student_sex'];?></td>
									</tr>
									<tr>
										<th>Corse </th>
										<td>
											<?php 
											$course_id = $this->Offer_model->multitableinfo('student',"id='$student_id'","course_id");
											echo $cours_name = $this->Offer_model->multitableinfo('course_details',"id='$course_id'","course_name");
											?>
										</td>
										<th>
											Course Code 
										</th>
										<td>
											<?php 
											echo $cours_code = $this->Offer_model->multitableinfo('course_details',"id='$course_id'","course_code");
											?>
										</td>
									<tr>
									<tr>
									<td colspan='4' style='padding-left:20px'>
										<h6 align='center'>Module Covered</h6>
										<?php 
										$semester = substr($exam_name,8);
										$course_id = $this->Offer_model->multitableinfo('student',"id='$student_id'","course_id");
										$cours_code = $this->Offer_model->multitableinfo('course_details',"id='$course_id'","course_code");
										 $syllabus = $this->Offer_model->multitableinfo('course_details',"id=".$row['course_id'],"course_syllabus");
										 $course_arr = explode(",",$syllabus);
										
											$this_syllabus = $this->Offer_model->multitableinfo('module',"module_name='$cours_code'and module_semester='$semester'","module_syllabus");
											echo "<i class='fa fa-arrow-right'></i> &nbsp; &nbsp; ". $this_syllabus ."<br>" ;
										
										
										?>
									</td>
									</tr>
									
								
								</table>
								<table  class='table table-bordered'>
									<tr style='background: #666;color:#fff;font-size:14px;font-weight:900'> <th colspan='6'> <center>Statement of Marks </center></th></tr>
									<tr>
										<th> Subject </th>
										<th> Written </th>
										<th> Practical /Lab </th>
										<th> Assignment/Project </th>
										<th> Viva/Interview </th>
										<th> Total </th>
									</tr>
									<tr>
										<td> Full Marks </td>
										<td> 100 </td>
										<td> 100 </td>
										<td> 100 </td>
										<td> 100 </td>
										<td> 400 </td>
									</tr>
									<tr>
										<td> Pass Marks </td>
										<td> 40 </td>
										<td> 40 </td>
										<td> 40 </td>
										<td> 40 </td>
										<td> 160 </td>
									</tr>
									<tr class='obtained'>
										<td> Marks Obtained</td>
										<td> <?php echo $a = $this->Offer_model->multitableinfo('result',"student_id='$student_id' and exam_name='$exam_name' and status not in('removed','EMPTY')",'written');?> </td>
										<td> <?php echo $b =  $this->Offer_model->multitableinfo('result',"student_id='$student_id' and exam_name='$exam_name' and status not in('removed','EMPTY')",'practical');?> </td>
										<td> <?php echo $c = $this->Offer_model->multitableinfo('result',"student_id='$student_id' and exam_name='$exam_name' and status not in('removed','EMPTY')",'assignment');?> </td>
										<td> <?php echo $d =  $this->Offer_model->multitableinfo('result',"student_id='$student_id' and exam_name='$exam_name' and status not in('removed','EMPTY')",'viva');?> </td>
										<td><?php echo $t = $a+$b+$c+$d;?></td>
									</tr>
								
									
										<?php 
										$last_semester = $this->Offer_model->multitableinfo('course_details',"id='$course_id'","no_of_semester");
										if($semester<>$last_semester)
										{
										?>
										<tr style='background: #666;color:#fff;font-size:14px;font-weight:900'>
										<td colspan='2'>
										<center>Issued On  : <b>
										    <?php 
										  //  echo nice_date($row['date_of_issue'],'d-M-Y') ;
									        $dt_issue = $this->Offer_model->multitableinfo('result',"student_id='$student_id' and exam_name='$exam_name' and status not in('removed','EMPTY')",'date_of_issue');
										    echo nice_date($dt_issue,'d-M-Y');
										    ?>
										    </b></center>
										</td>
										<td colspan='2'>
										<center>Remarks : <b><?php echo $row['comment'] ?></b></center>
										</td>
										<td colspan='3'>
										<center>Percentage (%)  : <b><?php echo round(($t/4),2); ?></b></center>
										</td>
										<?php 
										}else {
											?>
										<tr style='background: #666;color:#fff;font-size:14px;font-weight:900'>
											<td colspan='2'>Date Of Issue </td>
											<td align='center' colspan='2'>Precentage (%) </td>
											<td>Grade </td>
											<td>Remarks</td>
										</tr >
										<tr style='font-weight:800'>
											<td align='center' colspan='2'> <?php echo nice_date($row['date_of_issue'],'d-M-Y')?></td>
											
											<td align='center' colspan='2'> <?php echo $row['finalpercentage']?></td>
											
											<td> <?php echo $row['grade']?></td>
										 	<td><?php echo $row['comment'] ?></td>
										</td>
										</tr>
											<?php 
										}
										?>
									
									</table>
								
								<a href='result'><button class='btn btn-xs btn-primary'>Go Bck</button></a>
								</center>
							</div>
							</div>
							<?php 
								}
							
							else {
						
							echo "<h4 style='color:red' align='center' >Result is not declared yet.</h4>";
							echo "<center><a href='result'><button class='btn btn-xs btn-primary' style='margin-top:30px;' onClick='send' name='search' value='search'> Submit</button></a></center>";

							}
								}
								else {
							
								echo "<h4 style='color:red' align='center' >Record Not Found. Please Check Your Roll No. or Date Of Birth.</h4>";
								echo "<center><a href='result'><button class='btn btn-xs btn-primary' style='margin-top:30px;' onClick='send' name='search' value='search'> Submit</button></a></center>";

								}
							 } else { 
								?>
									<div class='row'>
								<form id='verifyForm' method='post' action=''>
							<div class='col-lg-3'>
							</div>
							<div class='col-lg-2'>
								<div class='form-grop'>
									<label>Enter Your Roll. No.* </label>
								<input type='number' name='student_roll' id='student_roll' class='form-control' placeholder='Enter You Reg. No.' required>
								</div>
							</div>
							<div class='col-lg-2'>
								<div class='form-grop'>
									<label>Center Code * </label>
								<select type='text' name='center_code' id='center_code' placeholder='BR/CPR/01' class='form-control' required>
									<option value=''>---- Select One ----</option>
									<?php 
									
										$cent_list =  $this->Offer_model->getarray('center_details',"status not in('removed','EMPTY')");
										foreach($cent_list as $center)
										{
												echo "<option value='$center[center_code]'>".$center['center_code']."</option>";
										}
									?>
								</select>
								</div>
							</div>
							<div class='col-lg-2'>
								<div class='form-grop'>
									<label>Seelct Semester * </label>
								<select name='exam_name' id='exam_name' class='form-control' required>
								<option value=''>-----Select One-----</optoin>
								<option value='semester 1'>Semester 1</optoin>
								<option value='semester 2'>Semester 2</optoin>
								<option value='semester 3'>Semester 3</optoin>
								<option value='semester 4'>Semester 4</optoin>
								</select>
								</div>
							</div>
							<div class='col-lg-1'>
								<div class='form-grop'>
								<button class='btn btn-xs btn-primary' style='margin-top:30px;' onClick='send' name='search' value='search'> Submit</button>
								</div>
							</div>
							</form>
							 <?php }?>
						</div>
					</div><!-- headline -->
					
					</div><!-- col -->
				</div><!-- row -->
			</div><!-- container -->
			
			<br>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-1"></div>
					<div class="col-sm-10">
						
						<div class="about-me">
							<div class="about-me-details">
							
						</div><!-- about-me -->
						</div><!-- about-me -->
						<center>
							***<br>
							*******<br>
							**************<br>
							**************************<br>
							
						</center>
					</div><!-- col -->
				</div><!-- row -->
			</div><!-- container -->
			
			<br>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						
						
						
					</div><!-- col -->
				</div><!-- row -->
			</div><!-- container -->
			
			<section class="full-section" id="section-6">
				
			</section><!-- full-section -->
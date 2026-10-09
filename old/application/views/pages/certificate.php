
		<!-- CONTENT -->
		<div id="page-content">
			
			<div id="page-header" class="parallax" data-stellar-background-ratio="0.1" style="background-image:url(<?php echo base_url();?>web/images/backgrounds/page-header-1.jpg);">
				
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							
							<h3>Certificate Verification</h3>
							
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
								
							<h2 align='center'><span class='text-primary'> </span><span class='text-warning'>Verify Your Certificate Here</span></h2><br>
						
							<?php 
								
								if(isset($_POST['search']))
								{
									extract($_POST);
									
									$res = $this->Offer_model->getarray('student',"student_roll='$student_roll' and date_of_birth='$date_of_birth'  and status not in ('removed','EMPTY')")[0];
								    
									if($res)
									{
										$row = $res;
								?>
									<div class='row'>
								<div class='col-lg-2'></div>
								<div class='col-lg-8'>
								<div class='table-responsive'>
									<center>
									<table class='table table-bordered'>
										<tr>
											<th>Registration No.</th>
											<td><?php echo $row['registration_no']?></td>
											<td colspan='2' align='center' rowspan='3' style='width:115px'><img src='<?php echo base_url();?>uploads/<?php echo $row['student_photo'];?>' class='img-thumbnail' width='120px'></td>
										</tr>
										<tr>
											<th>Name Of Center</th>
											<td><?php echo $this->Offer_model->multitableinfo('center_details',"id=".$row['center_id'],"center_name");?></td>
											
										</tr>
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
											<th>Email Id</th>
											<td><?php echo $row['student_email'];?></td>
											<th>Mobile No.</th>
											<td><?php echo $row['student_mobile'];?></td>
											
										<tr>
										</tr>
											<th>Address</th>
											<td><?php echo $row['student_address'];?></td>
											<th>Course</th>
											<td><?php echo $this->Offer_model->multitableinfo('course_details',"id=".$row['course_id'],"course_code");?></td>
											
										</tr>
                                        </tr>
                                            <th>Duration</th>
											<td><?php echo $this->Offer_model->multitableinfo('course_details',"id=".$row['course_id'],"no_of_semester");?> Semester(s)</td>
											<th>Certificate No.</th>
											<td><?php echo $row['certificate_no'];?></td>
										</tr> 
                                         </tr>
                                            <th>Date Of Issue</th>
											<td><?php echo nice_date($row['date_of_issue'],'d-M-Y');?></td>
											<th>Final Percentage</th>
											<td><?php echo $row['finalpercentage'];?> %</td>
										</tr>
                                         </tr>
                                            <th>Grade</th>
											<td><?php echo $row['grade'];?></td>
											<th>Remarks</th>
											<td><?php echo $row['comment'];?></td>
										</tr>
									</table><br>
									<a href='certificate'><button class='btn btn-xs btn-primary'>Go Bck</button></a>
									</center>
								</div>
								</div>
								<?php 
									}
									else {
								
									echo "<i><h4 style='color:red' align='center' >No Record Found. Please Check Your Certificate No. or Date Of Birth.</h4></i>";
									echo "<center><a href='certificate'><button class='btn btn-xs btn-primary' style='margin-top:30px;' onClick='send' name='search' value='search'> Go Back</button></a></center>";

									}
								 } else { 
									?>
										<div class='row'>
									<form id='verifyForm' method='post' action=''>
								<div class='col-lg-3'>
								</div>
								<div class='col-lg-3'>
									<div class='form-grop'>
										<label>Enter Your Roll. No.* </label>
									<input type='number' name='student_roll' id='student_roll' class='form-control' placeholder='Enter Your Roll. No.' required>
									</div>
								</div>
								<div class='col-lg-3'>
									<div class='form-grop'>
										<label>Date Of Birth * </label>
									<input type='date' name='date_of_birth' id='date_of_birth' class='form-control' required>
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
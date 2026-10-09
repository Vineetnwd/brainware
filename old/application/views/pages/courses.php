
		<!-- CONTENT -->
		<div id="page-content">
			
			<div id="page-header" class="parallax" data-stellar-background-ratio="0.1" style="background-image:url(<?php echo base_url();?>web/images/backgrounds/page-header-1.jpg);">
				
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							
							<h3>Courses</h3>
							
						</div><!-- col -->
					</div><!-- row -->
				</div><!-- container -->    
				
				<a class="go-to-section" href="#go-to-section"><i class="fa fa-angle-down"></i></a>
				
			</div><!-- page-header -->
			
			<div id="go-to-section"></div>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
					
						<div class="headline">
								
							<h2><span class='text-primary'> </span><span class='text-warning'>List Of Courses</span></h2>
							<p></p>
						
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
						<?php
							$course_arr = $this->Offer_model->getarray('course_details',"status not in('removed','EMPTY')");
							
							foreach($course_arr as $row)
							{
							?>
								<div class='col-lg-4'>
									<div class='panel panel-primary'>
										<div class='panel-heading'>
											<?php echo $row['course_name'];?>
										</div>
										<div class='panel-body'>
											<div class="alert alert-warning" role="alert">
											<strong><?php echo $row['course_duration'];?></strong> 
											</div>
											<?php 
											
											$id_list = $row['course_syllabus'];
											
												$id_arr = explode(",",$id_list);
												
												foreach($id_arr as $item1)
												{
														
													$syl_list = explode(",",$this->Offer_model->multitableinfo('module',"id='$item1' and status in ('ACTIVE')","module_syllabus"));
													foreach($syl_list as $syl)
													{
														echo $syl.'<br>';
													}

												}
											?>
										</div>
									</div>
								</div>
							<?php 		
							}
							?>
						
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
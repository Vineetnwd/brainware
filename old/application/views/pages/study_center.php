
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
					
					
						<div class="about-me">
							<div class="about-me-details">
							
						</div><!-- about-me -->
						
						</div><!-- about-me -->
						
								<div class='col-lg-12'>
									<div class='panel panel-primary'>
										<div class='panel-heading'>
											Our Study Centers
										</div>
										<div class='panel-body'>
											<!-- <div class="alert alert-warning" role="alert"> -->
                                            <div class='table-responsive'>
											<table class='table table-bordered'>
                                                <thead>
                                                    <th>Sr. No. </th>
                                                    <th>Center Name </th>
                                                    <th>Center Code </th>
                                                    <th>Mobile No. </th>
                                                    <th>Center Email </th>
                                                    <th>Center Address </th>
                                                    <th>Director</th>
                                                    <th>Photo</th>
                                                   
                                                </thead>
											<?php 
										
												$center_list = $this->Offer_model->getarray('center_details',"status in('ACTIVE')");
												$i=1;
												foreach($center_list as $center)
												{
                                                    echo "<tr>";
                                                    echo "<td>" .$i. "</td>";
                                                    echo "<td>" .$center['center_name']. "</td>";
                                                    echo "<td>" .$center['center_code']. "</td>";
                                                    echo "<td>" .$center['center_mobile']. "</td>";
                                                    echo "<td>" .$center['center_email']. "</td>";
                                                    echo "<td>" .$center['center_address']. "</td>";
                                                    echo "<td>" .$center['center_director']. "</td>";
                                                    echo "<td><img src=".base_url().'uploads/'.$center['center_photo']." height='50px'></td>";
                                                    echo "</tr>";
                                                    $i++;
												}
											?>
										
                                        </table>
                                        </div>
									</div>
									</div>
								</div>
						
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
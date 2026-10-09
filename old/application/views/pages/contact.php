
			
		<!-- CONTENT -->
		<div id="page-content">
			
			<div id="page-header" class="parallax" data-stellar-background-ratio="0.1" style="background-image:url(<?php echo base_url();?>web/images/backgrounds/page-header-1.jpg);">
				
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							
							<h3>Get In Touch</h3>
							
						</div><!-- col -->
					</div><!-- row -->
				</div><!-- container -->
				
				<a class="go-to-section" href="#go-to-section"><i class="fa fa-angle-down"></i></a>
				
			</div><!-- page-header -->
			
			<div id="go-to-section"></div>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-5">
						
						<div class="title">
							<h4>Address Info</h4>
						</div><!-- title -->
						
						<br>
						
						<div class="widget widget-contact">

                            <ul>
                                <li class="address">
									<span>Address</span>
                                   <?php echo ucwords(config('sc_code'));?><br>
                                   <?php echo ucwords(config('sc_address1'));?><br>
                                   <?php echo ucwords(config('sc_district'));?>, <?php echo ucwords(config('sc_state'));?> - <?php echo ucwords(config('sc_pin'));?>,
                                </li>
                                <li class="phone">
									<span>Phone / Mobile No.</span>
                                    <a href='tel:<?php echo str_replace(" ","",config('sc_contact1'));?>,'><?php echo ucwords(config('sc_contact1'));?>
                                </li>
                                <li class="email">
									<span>Email</span>
                                    <a href="mailto:<?php echo ucwords(config('sc_email'));?>,"><?php echo ucwords(config('sc_email'));?></a>
                                </li>
                            </ul>

                        </div><!-- widget-contact -->
						
						<div class="title">
							<h4>Social Networks</h4>
						</div><!-- title -->
						
						<br>
						
						<p>You Can Find Un On Social Networks</p>
						
						<div class="social-media">
							<a class="facebook" href="#"><i class="fa fa-facebook"></i></a>
							<a class="twitter" href="#"><i class="fa fa-twitter"></i></a>
							<a class="behance" href="#"><i class="fa fa-youtube"></i></a>
						</div><!-- social-media -->
						
					</div><!-- col -->
					<div class="col-sm-7">	
						
						<div class="title">
							<h4>Contact Form</h4>
						</div><!-- title -->
						<div class='text-danger'>
						<?php if (isset($success_message)) {
						echo ucwords($success_message);
						}
						?>
					</div>
					<?php if ($this->session->flashdata('success_message')) {
							?>
					<div class="alert alert-success" style='width:100%'>
					  <strong> <i class='fa fa-check-circle'></i> <?php 
					  echo ucwords($this->session->flashdata('success_message'));
					  $this->session->unset_userdata('success_message');
					  ?>
					  </strong>
					</div>
					<?php }?>
						<div class="clearfix">
						<?php echo form_open(base_url().'index.php/pages/enquiry');?>
                            <fieldset>

                                <div id="alert-area"></div>

								<?php echo form_input(array('name'=>'e_name','id'=>'e_name','class'=>'col-xs-12','placeholder'=>'Your Full Name *'),set_value('e_name'));
							 echo form_error('e_name');
						 ?>

<?php echo form_input(array('name'=>'e_mobile','id'=>'e_mobile','class'=>'col-xs-12','placeholder'=>'10 Digit Mobile No. *'),set_value('e_mobile'));
							 echo form_error('e_mobile');
						 ?>
                               <?php echo form_input(array('name'=>'e_email','id'=>'e_email','class'=>'col-xs-12','placeholder'=>'Your Email Id *'),set_value('e_email'));
							 echo form_error('e_email');
						 ?>
<?php 
							 echo form_textarea(array('name'=>'e_question','id'=>'e_question','class'=>'col-xs-12','placeholder'=>'Write Your Query Here ....'),set_value('e_question'));
							 echo form_error('e_question');
						 ?>
                                <!-- <textarea class="col-xs-12" id="message" name="message" rows="5" cols="25" placeholder="Message"></textarea> -->

							</fieldset>
							
                            <?php echo form_submit(array('id'=>'end_submit','value'=>'Send your message','class'=>'btn btn-warning'));
						echo form_close();
									?>
                        </form>
						
					</div><!-- col -->
				</div><!-- row -->
			</div><!-- container -->
			
			<br><br><br>
			
			<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14370.45101769189!2d84.7458984!3d25.7833514!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x5069cf37643a622f!2sbrainware%20computer%20academy!5e0!3m2!1sen!2sin!4v1578392483935!5m2!1sen!2sin" width="100%" height="350" frameborder="0" style="border:0;" allowfullscreen=""></iframe>

			
		</div><!-- PAGE CONTENT -->
		
		
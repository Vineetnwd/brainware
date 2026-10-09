<?php 
$this->load->view('pages/header');
?>			
		<!-- CONTENT -->
		<div id="page-content">
			
			
			
			<div id="go-to-section"></div>
			
			<div class="container">
				<div class="row">
					<div class="col-sm-offset-2 col-sm-8 col-md-offset-3 col-md-6">
						
						<h1 class="error">404</h1>
						
						<h3 class="text-center"><strong>The page you are looking for <br class="hidden-xs"> does not exist</strong></h3>
						
						<br>
						
						<p class="text-center">You are looking for unavailable page.</p>
						
						<br>
						
						<p class="text-center"><a class="btn btn-warning" href="<?php echo base_url();?>">Go back home</a></p>
						
					</div><!-- col -->
				</div><!-- row -->
			</div><!-- container --> 
			
			<br><br>
			
		</div><!-- PAGE CONTENT -->
		
		
		<?php 
		$this->load->view('pages/footer');
		?>
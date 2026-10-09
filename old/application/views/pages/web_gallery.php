<!-- CONTENT -->
<div id="page-content">
<div id="page-header" class="parallax" data-stellar-background-ratio="0.1" style="background-image:url(<?php echo base_url();?>web/images/backgrounds/page-header-1.jpg);">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <h3>Web Gallery</h3>
         </div>
         <!-- col -->
      </div>
      <!-- row -->
   </div>
   <!-- container -->    
   <a class="go-to-section" href="#go-to-section"><i class="fa fa-angle-down"></i></a>
</div>
<!-- page-header -->
<div id="go-to-section"></div>
<div class="container">
   <div class="row">
      <div class="col-sm-12">
         <div class="headline">
            <h2><span class='text-primary'> </span><span class='text-warning'>Web Gallery </span></h2>
            <p></p>
         </div>
         <!-- headline -->
      </div>
      <!-- col -->
   </div>
   <!-- row -->
</div>
<!-- container -->
<br>
<div class='row'>
<div class='col-lg-12'>
   <div class='row'>
      <div class='col-lg-1'></div>
      <div class='col-lg-10'>
      <div class='row'>
         <?php 
         $img_arr = $this->Offer_model->getarray('gallery',"status not in('removed','EMPTY')");
         if($img_arr)
         {
         foreach($img_arr as $row)
         {
         ?>
      <div class='col-lg-3'>
         <div class="item">
            <div class="portfolio-item">
               <div class="portfolio-item-thumbnail">
                  <img src="<?php echo base_url();?>uploads/<?php echo $row['image_url'];?>" alt="">
                  <div class="portfolio-item-hover">
                     <div class="portfolio-item-description">
                        <h4><a href="portfolio-single.html"><?php echo $row['image_title'];?></a></h4>
                        <p><?php echo $row['image_description'];?></p>
                     </div>
                     <!-- portfolio-item-description -->
                     <a class="fancybox zoom-action" data-fancybox="portfolio" href="<?php echo base_url();?>uploads/<?php echo $row['image_url'];?>">+</a>
                  </div>
                  <!-- portfolio-item-hover -->
               </div>
               <!-- portfolio-item-thumbnail -->
            </div>
            <!-- portfolio-item -->
         </div>
         </div>
         <?php }}?>
         <!-- item -->
     
      <!-- item -->
   </div>
   <!-- portfolios-slider -->
</div>
<br>
<div class="container">
   <div class="row">
      <div class="col-sm-12">
      </div>
      <!-- col -->
   </div>
   <!-- row -->
</div>
<!-- container -->
<section class="full-section" id="section-6">
</section>
<!-- full-section -->
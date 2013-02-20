 <h1>Residential Property</h1>
 <?php foreach($property as $home): ?>

  <div class="listingContainer">
					<h2><?php echo $home['address'];?></h2>
					<div class="listingAttributes">
						<div class="col0">
						<span class="name"><?php echo $home['bedroomsName'];?>:</span>
							<span class="value"><?php echo $home['bedroomsValue'][0];?></span>

						<span class="name"><?php echo $home['bathroomsName'];?>:</span>
							<span class="value"><?php echo $home['bathroomsValue'][0];?></span><br/>

								<span class="name"><?php echo $home['priceName'];?>:</span></br>
							<span class="value"><?php echo $home['priceValue'];?></span></p>															
						</div>
						<div class="col1">
						<span class="name"><?php echo $home['floorName'];?>:</span>
							<span class="value"><?php echo $home['floorValue'];?></span>
						<span class="name"><?php echo $home['landName'];?>:</span>
							<span class="value"><?php echo $home['landValue'];?></span>
						<span class="name"><?php echo $home['rateName'];?></span>
							<span class="value"><?php echo $home['rateValue'];?></span>																		
						</div>
								<div class="col2">
							<span class="name"><?php echo $home['openName'];?>:</span>
							<span class="value">
								<?php 
								if(!empty($home['openValue']))
								{
									echo '<p>No Open Homes</p>'; 	
								}else{
									 	foreach($home['openValue'] as $time)
								 	 	{
								 	 		echo '<p><?php echo $time; ?></p>';
								 	 	}
								}
								 ?>		
							</span>
						</div>

						<div class="col3">
							<span class="name"><?php echo $home['areaName'];?>:</span><br/>
							<span class="value"><?php echo $home['areaValue'];?></span>
						</div>
						<div class="col4">
							<span class="name"><?php echo $home['parkName'];?>:</span><br/>
							<span class="value"><?php echo $home['parkValue'];?></span>
						</div>
					</div>	
						<div class="description">
							<span class="value text"><?php echo $home['description'];?></span>
						</div>

						<div class="gallery">
                    		<div class="flexslider loading">
								<ul class="slides">
									<?php foreach($home['photos'] as $key => $photo): ?>
											<li><img src="<?php echo $photo;?>" width="960" height="380" alt="" /></li>
									<?php endforeach; ?>
								</ul>
            				</div>
            			</div> 
						</div><!-- listingAttributes End -->
<?php endforeach;?>						
<h1>Subdivisions </h1>

<?php if(empty($property)): ?>
	<h3>Sorry we currently have no Subdivision sections for sale</h3>
<?php else: ?>

<?php foreach($property as $section):?>
	<h2 class="sectionTitle"><?php echo $section['locationValue']?></h2>
					<div class="sectionListingAttributes">
						<div class="col0">
								<span class="name"><?php echo $section['priceName'];?>:</span>
								<span class="value"><?php echo $section['priceValue'];?></span><br/>
								<span class="name"><?php echo $section['landAreaName'];?>:</span>
								<span class="value"><?php echo $section['landAreaValue'];?></span>																	
						</div>
					</div>	
						<div class="description">
							<span class="value text">
								<?php echo $section['description']; ?>
							</span>
						</div>

						<div class="gallery">
                    		<div class="flexslider loading">
								<ul class="slides">
									<?php foreach($section['photos'] as $photo): ?>
										<li><img src="<?php echo $photo[0];?>" width="960" height="380" alt="" /></li>
									<?php endforeach;?>	
								</ul>
            				</div>
						</div><!-- listingAttributes End -->											
<?php endforeach;?>
<?php endif;?>	
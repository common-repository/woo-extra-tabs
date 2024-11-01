<?php
function odude_cross_check_categoris($product_category)
{
	$a=array();	
	 
	// Custom query.
	$query1 = new WP_Query( array('post_type' => 'woo-extra-tab') );
 
	// Check that we have query results.
	if ( $query1->have_posts() ) 
	{
 
   
        while($query1->have_posts()) : $query1->the_post();
		
			$terms1 = get_the_terms( $query->ID, 'product_cat' );
			$x="";
			
			foreach ((array) $terms1 as $term) 
			{
				$tab_category = $term->name;
				if($product_category==$tab_category && !empty($product_category))
				{
					//echo the_title()." match category ".$product_category."<br>";
										
					$x=get_the_ID();
					array_push($a,$x);
				}
			}
			
		endwhile;
 
   
 
	}
//print_r($a);
// Restore original post data.
wp_reset_postdata();
return $a;
	
}
?>
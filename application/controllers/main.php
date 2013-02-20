<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		
	}

	public function index()
	{
		$this->load->view('main');
	}

	//Open2View

	public function getOpen2ViewInfotmation()
	{
		$json = 'https://api.open2view.com/nz/properties.json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $json);  
		curl_setopt($ch, CURLOPT_USERPWD, "brianmahoney:09sh39vha3");
		curl_error($ch);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$information = json_decode($result, true);
		

		$idArray = array();
		$infoArray = array();
		$photosArray = array();

		foreach ($information as $info) 
		{
			foreach($info['property'] as $property)
			{
				$idArray[] = array('open2ViewPropId' => $property['id']);

			}
		}

		foreach ($idArray as $id) 
		{
			$json = 'https://api.open2view.com/nz/properties.json?id='.$id['open2ViewPropId'].'&detail=full';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $json);  
			curl_setopt($ch, CURLOPT_USERPWD, "brianmahoney:09sh39vha3");
			curl_error($ch);
			$result = curl_exec($ch);
			curl_close($ch);

			$information = json_decode($result, true);

			foreach ($information as $info) {

				$streetAddress = $info['property']['address']['address'];
				$suburb 	   = $info['property']['suburb'];
				$region 	   = $info['property']['region'];
				$address       = $streetAddress.', '.$suburb.', '.$region;
				$photosO  	   = $info['property']['photos']['photo'];

				foreach($photosO as $photo)
				{
					$photosArray[] = array('photo' => $photo['url']);
				}
				
				$infoArray[] = array('address' => $address, 'photos' => $photosArray);
			}
		}

		return $infoArray;
	}

	// Gets information from the members listing page to generate the specific listing by category

	private function getTMMemberListings()
	{
		$json = file_get_contents("http://api.trademe.co.nz/v1/Member/4389666/Listings/All.json");
		$listings = json_decode($json, true);

		$listInfo = array();

		foreach ($listings['List'] as $listing) {
				
				$listInfo[] = array(
					                 'listingId'       => $listing['ListingId'], 
					                 'listingCategory' => $listing['Category'], 
					                 'listingTitle'    => $listing['Title']
					                );
		}

		return $listInfo;
	}

	private function getTMCatInfomation()
	{
	  $json = file_get_contents('http://api.trademe.co.nz/v1/Categories/0350.json');
	  $Categories =  json_decode($json, true);

		 $catInfo = array();

		 foreach ($Categories['Subcategories'] as $parent)
		  {

		  	if (!empty($parent['Subcategories']) && is_array($parent['Subcategories']))
			{
				foreach ($parent['Subcategories'] as $child) 
				{
					//parentNumber is Subcategories Number

					$catInfo[] = array('parentName'   => $parent['Name'], 
									   'subName'      => $child['Name'], 
									   'parentNumber' => $child['Number']);
				}
			}else{
				$catInfo[] = array('parentName'   => $parent['Name'], 
								   'parentNumber' => $parent['Number'] );

			}
		}
		return $catInfo;
	}

	private function createCategoryDetails()
	{
		$getCategory     = $this->getTMCatInfomation();
		$getListCategory = $this->getTMMemberListings();

		$listingInfo = array();
		foreach($getListCategory as $lc)
		{
			foreach($getCategory as $gc)
			{
				if($lc['listingCategory'] == $gc['parentNumber'])
				{
					$listingInfo[] = array(
											'listingId'    => $lc['listingId'],
											'mainCat'	   => $gc['parentName'],
											'subCat'	   => $gc['subName']	
										);

				}

			}
		}
		return $listingInfo;
	}

	public function propertyDetailsSections()
	{
		$categoryDetails 		= $this->createCategoryDetails();
		$open2ViewInformation	= $this->getOpen2ViewInfotmation();

		$category = array();
		$details = array();

		foreach ($categoryDetails as $id) {
				
			$Json = file_get_contents('http://api.trademe.co.nz/v1/Listings/'.$id['listingId'].'.json');
			$dataDecode = json_decode($Json, true);

			$data = array($dataDecode);

			$photos = array();

			foreach ($data as $key => $detail) 
			{
				$catNameO = $detail['CategoryName'];
				$catName = str_replace(" ", "-", $catNameO);

				$defaultDescription = $detail['Body'];
				$description = preg_replace('/\s\s+/','<br/><br/>', $defaultDescription);

				if($catName == 'Sections-for-sale')
				{	
					$streetAddressO = $detail['Attributes']['3']['Value'];
					$street         = explode(" ", $streetAddressO);
					$suburb         = $detail['Attributes']['5']['Value'];	
					$region         = $detail['Attributes']['6']['Value'];
					$address = $street[0]." ".$street[1]." ".$street[2]." ".$suburb.", ".$region;

					foreach($open2ViewInformation as $open)
					{
						if($open['address'] == $address)
						{
							$photos[] = array($open2ViewInformation[0]['photos']);
						}else{
								
								foreach($detail['Photos'] as $key => $image)
								{
									$photos[] = array($image['Value']['FullSize']);
								}
						}
					}

					$details[]   = array( 'priceName'		=> $detail['Attributes'][2]['DisplayName'],
										'priceValue'    => $detail['Attributes'][2]['Value'],
										'locationName'  => $detail['Attributes'][3]['DisplayName'],
										'locationValue' => $detail['Attributes'][3]['Value'],
										'landAreaName'  => $detail['Attributes'][7]['DisplayName'],
										'landAreaValue' => $detail['Attributes'][7]['Value'],
										'description' 	=> $description,
										'photos'		=> $photos
									  );
				}
				
			}
				
		}
		return $details;
	}
	public function propertyDetailsHouse()
	{
		$categoryDetails 		= $this->createCategoryDetails();
		$open2ViewInformation	= $this->getOpen2ViewInfotmation();

		$category = array();
		$details = array();

		foreach ($categoryDetails as $id) {
			//$this->output->cache(10);
			$Json = file_get_contents('http://api.trademe.co.nz/v1/Listings/'.$id['listingId'].'.json');
			$dataDecode = json_decode($Json, true);

			$data = array($dataDecode);

			$photos = array();

			foreach ($data as $key => $detail) 
			{
				$catNameO = $detail['CategoryName'];
				$catName = str_replace(" ", "-", $catNameO);

				$defaultDescription = $detail['Body'];
				$description = preg_replace('/\s\s+/','<br/><br/>', $defaultDescription);

				if($catName == 'For-Sale')
				{	
					$streetAddressO = $detail['Attributes']['5']['Value'];
					$street         = explode(" ", $streetAddressO);
					$suburb         = $detail['Attributes']['7']['Value'];	
					$region         = $detail['Attributes']['8']['Value'];
					$address = $street[0]." ".$street[1]." ".$street[2]." ".$suburb.", ".$region;

					foreach($open2ViewInformation as $open)
					{

						if($open['address'] == $address)
						{

							$photos[] = array($open2ViewInformation[0]['photos']);
						}else{
								
								foreach($detail['Photos'] as $key => $image)
								{
									$photos[] = $image['Value']['FullSize'];
								}
						}
					}

					$openHomeTimes = array();

					if(!empty($detail) && in_array('OpenHomes', $detail))
					{
						foreach ($detail['OpenHomes'] as $openHome) 
						{	
							$startO        = $openHome['Start'];
							$finishO 	   = $openHome['End'];
							$startConvert  = preg_replace('~\D~', '', $startO);
							$start         = date('D j M g a',$startConvert / 1000);
							$finishConvert = preg_replace('~\D~', '', $finishO);
							$finish 	   = date('g:ia',$finishConvert / 1000);

							$openHomeDetail = $start." - ". $finish;

							$openHomeTimes[] = $openHomeDetail;

							
						}
					}

					if(empty($openHomeTimes))
					{
						$message = 'No Open Homes';

						$openHomeTimes = $message;

					}

					$bathroomsFull = $detail['Attributes']['0']['Value'];
					$bathrooms 	   = explode(' ', $bathroomsFull);
					$bedroomsFull  = $detail['Attributes']['1']['Value'];
					$bedrooms 	   = explode(' ', $bedroomsFull);		

					$details[]   = array( 
										'bedroomsName'   => $detail['Attributes']['0']['DisplayName'],
										'bedroomsValue'  => $bedrooms,
										'bathroomsName'  => $detail['Attributes']['1']['DisplayName'],
										'bathroomsValue' => $bathrooms,
										'rateName'		 => $detail['Attributes']['3']['DisplayName'],
										'rateValue'		 => $detail['Attributes']['3']['Value'],
										'priceName'		 => $detail['Attributes']['4']['DisplayName'],
										'priceValue'	 => $detail['Attributes']['4']['Value'],
										'floorName'		 => $detail['Attributes']['9']['DisplayName'],
										'floorValue'	 => $detail['Attributes']['9']['Value'],
										'landName'		 =>	$detail['Attributes']['10']['DisplayName'],
										'landValue'		 => $detail['Attributes']['10']['Value'],
										'areaName'		 => $detail['Attributes']['12']['DisplayName'],
										'areaValue'		 =>	$detail['Attributes']['12']['Value'],
										'parkName'		 => $detail['Attributes']['13']['DisplayName'],
										'parkValue'		 =>	$detail['Attributes']['13']['Value'],
										'openName'		 => 'Open home times',
										'openValue'		 => $openHomeTimes,
										'address'		 => $address,	
										'description' 	 => $description,
										'photos'	 	 => $photos
									  );
				}
				
			}
				
		}
		
		return $details;
	}


	public function createUrl()
	{
		$detailsFunction = $this->createCategoryDetails();

		$ld = array();
		$details = array();

    foreach ($detailsFunction as $main) 
    {
            $mainlisting = $main['listingId'];
            $mainCat     = strtolower($main['mainCat']);
            $subCatO     = strtolower($main['subCat']);
            $subCat      = str_replace(" ", "-", $subCatO);

            if(!array_key_exists($subCat, $details))
            {
    
   		   	  $details[$subCat] = array('url'     => base_url().'listings/'.$mainCat.'/'.$subCat,
      	  				 	 'mainCat' => $mainCat,
      	  				     'subCat'  => $subCat	
      	  				    );
   		   	}
	}
    	return $details;
	}

	public function pages()
	{
		//$this->output->cache(5);
		$urlMainCat= $this->uri->segment(2);
		$urlSubCat = $this->uri->segment(3);
		$url = $urlMainCat.'/'.$urlSubCat;

		if($url ==  'residential/for-sale')
		{
			$data['property'] = $this->propertyDetailsHouse();
			$data['pageTitle'] = 'Residential Property | BMGL';
			$this->load->view('partials/header', $data);
			$this->load->view('home', $data);
			$this->load->view('partials/footer');
		}

		if($url == 'residential/sections-for-sale')
		{
			$data['property'] = $this->propertyDetailsSections();
			$data['pageTitle'] = 'Subdivisions | BMGL';
			$this->load->view('partials/header', $data);
			$this->load->view('sections', $data);
			$this->load->view('partials/footer');


		}

	}

}


/* End of file main.php */
/* Location: ./application/controllers/main.php */
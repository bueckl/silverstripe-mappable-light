<?php
/**
 * LatLongField
 * Includes work from https://github.com/gordonbanderson/Mappable/blob/dev30/code/LatLongField.php
 */
class LatLongField extends FieldGroup {

	static $allowed_actions = array (
		'geocode'
	);

	protected $addressFields = array();

	protected $latField;

	protected $longField;

	protected $zoomField;

	protected $buttonText;

	public function __construct( $children = array(), $addressFields = array(), $buttonText = null ) {
		if ( ( sizeof( $children ) < 2 ) || ( !$children[0] instanceof FormField ) || ( !$children[1] instanceof FormField ) ) {
			user_error( 'LatLongField argument 1 must be an array containing at least two FormField objects for Lat/Long values, respectively.', E_USER_ERROR );
		}
		parent::__construct( $children );
		$this->addressFields = $addressFields;

		$this->buttonText = $buttonText ? $buttonText : _t( 'LatLongField.LOOKUP', 'Search' );
		$this->latField = $children[0]->getName();
		$this->longField = $children[1]->getName();

		if ( sizeof( $children ) == 3 ) {
			$this->zoomField = $children[2]->getName();
		}
		$name = "";
		foreach ( $children as $field ) {
			$name .= $field->getName();
		}

		// hide the lat long and zoom fields from the interface
		foreach ( $this->FieldList() as $fieldToHide ) {
			//$fieldToHide->addExtraClass( 'hide' );
		}



		$this->name = $name;
	}


	public function hasData() {return true;}
	
	/**
	 * Field Holer
	 */
	public function FieldHolder( $properties = array() ) {
		Requirements::javascript( THIRDPARTY_DIR.'/jquery/jquery.js' );
		Requirements::javascript( THIRDPARTY_DIR.'/jquery-livequery/jquery.livequery.js' );
		Requirements::javascript( THIRDPARTY_DIR.'/jquery-metadata/jquery.metadata.js' );

		$js = '
		<script type="text/javascript">
var latFieldName = "'.$this->latField.'";
var lonFieldName = "'.$this->longField.'";
var zoomFieldName = "'.$this->zoomField.'";
		</script>
	';

		Requirements::javascript('mappable-light/javascript/mapField.js' );
		Requirements::css('mappable-light/css/mapField.css');

		$attributes = array(
			'class' => 'editableMap',
			'id' => 'GoogleMap',
			'data-LatFieldName' => $this->latField,
			'data-LonFieldName' => $this->longField,
			'data-ZoomFieldName' => $this->zoomField,
			'data-UseMapBounds' => false
     );

        
		$guidePointsJSON = '';
		if (isset($this->guidePoints)) {
			$guidePointsJSON = json_encode($this->guidePoints);
			$attributes['data-GuidePoints'] = $guidePointsJSON;

			// we only wish to change the bounds to those of all the points iff the item currently has no location
			//$attributes['data-useMapBounds'] = true;
			error_log('**** GUIDE POINTS SET ****');
			error_log($guidePointsJSON);
		}
		
		//$attributes['style'] = 'height:300px;width:300px;';
		
		//Map
		//NOTE: The map sometimes has problems on first render
		//temporary fix is to resize the window
		$content = '<div class="editableMapWrapper">' . $this->createTag(
				"div",
				$attributes
		) . '</div>';

		$this->FieldList()->push( new LiteralField( 'locationEditor', $content ) );



		$content2 = '<div id="mapSearch">
		 <!--<input name="location_search" id="location_search" size=80/>-->
    	<button class="action" id="searchLocationButton">Search Location Name</button>
      		<div id="mapSearchResults">
      	</div>
    </div>
    ';

		
		$latField = $this->FieldList()->fieldByName($this->latField);
		
		//$this->FieldList()->push( new LiteralField( 'mapSearch', $content2 ) );
		$this->FieldList()->insertBefore( new LiteralField( 'mapSearch', $content2 ), $this->latField );
		
		
		return parent::FieldHolder();
	}

	public function geocode( SS_HTTPRequest $r ) {
		if ( $address = $r->requestVar( 'address' ) ) {
			if ( $json = @file_get_contents( "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode( $address ) ) ) {
				$response = Convert::json2array( $json );
				$location = $response['results'][0]->geometry->location;
				return new SS_HTTPResponse( $location->lat.",".$location->lng );
			}
		}
	}

	/*
	Set guidance points for the map being edited.  For example in a photographic set show the map position of some other images
	so that subsequent photo edits do not start with a map centred on the horizon
	*/
	public function setGuidePoints($guidePoints) {
		$this->guidePoints = $guidePoints;
	}

}




//class LatLongField extends FieldGroup {
//		
//	static $allowed_actions = array (
//		'geocode'
//	);
//	
//	protected $addressFields = array();
//	
//	protected $latField;
//	
//	protected $longField;
//			
//	protected $buttonText;
//	
//	public function __construct($children = array(), $addressFields = array(), $buttonText = null) {
//		if((sizeof($children) < 2) || (!$children[0] instanceof FormField) || (!$children[1] instanceof FormField)) {
//			user_error('LatLongField argument 1 must be an array containing at least two FormField objects for Lat/Long values, respectively.',E_USER_ERROR);
//		}
//		parent::__construct($children);	
//		$this->addressFields = $addressFields;
//		$this->buttonText = $buttonText ? $buttonText : _t('LatLongField.LOOKUP','Look up');
//		$this->latField = $children[0]->getName();
//		$this->longField = $children[1]->getName();
//		$name = "";
//		foreach($children as $field) {
//			$name .= $field->getName();
//		}
//
//		$this->setName($name);
//	}
//	
//	
//	public function hasData() {return true;}
//	
//	public function FieldHolder($properties = array()) {
//		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
//		Requirements::javascript(THIRDPARTY_DIR.'/jquery-metadata/jquery.metadata.js');
//		Requirements::javascript('mappable/javascript/lat_long_field.js');
//		Requirements::css('mappable/css/lat_long_field.css');
//		$this->FieldList()->push(new LiteralField('geocode_'.$this->id(), sprintf('<a class="geocode_button {\'aFields\': \'%s\',\'lat\': \'%s\', \'long\': \'%s\'}" href="'.$this->Link('geocode').'">'.
//							$this->buttonText.
//						'</a>', implode(',',$this->addressFields), $this->latField, $this->longField)));
//                $map = GoogleMapUtil::instance();
//                $map->setDivId('geocode_map_'.$this->id());
//                $map->setEnableAutomaticCenterZoom(false);
//
//                $mapHtml = $map->forTemplate();
//
//                $this->FieldList()->push(new LiteralField ('geocode_map_field'.$this->id(),$mapHtml));
//		return parent::FieldHolder($properties = array());
//	}
//	
//	public function geocode(SS_HTTPRequest $r) {
//		if($address = $r->requestVar('address')) {
//			if($json = @file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($address))) {
//				$response = Convert::json2array($json);
//				$location = $response['results'][0]->geometry->location;
//				return new SS_HTTPResponse($location->lat.",".$location->lng);
//			}
//		}
//	}
//		
//}
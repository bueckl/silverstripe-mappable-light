<?php

/*
 * Provides a GoogleMap() function to ViewableData objects.
 *
 * @author Uncle Cheese
 * @package mappable
 */
class MappableData extends Extension {
	
	/**
	 * Renders a Google Map
	 * Uses and outdated API, so might be good to replace that API with something like this:
	 * https://github.com/anselmdk/silverstripe-googlemapspage/blob/anselmdk/code/GoogleMapsDecorator.php
	 * or this:
	 * https://github.com/egeloen/ivory-google-map (seems interesting, and updated, and can be added via Composer)
	 * or this:
	 * http://www.symfony-project.org/plugins/sfEasyGMapPlugin
	 * 
	 * @param int $width
	 * @param int $height
	 * @return sring
	 */
	public function GoogleMap($width = null, $height = null) {
		$gmap = GoogleMapUtil::get_map(new ArrayList($this->owner));
		$w = $width ? $width : GoogleMapUtil::$map_width;
		$h = $height ? $height : GoogleMapUtil::$map_height;
		$gmap->setSize($w,$h);
		$gmap->setEnableAutomaticCenterZoom(false);
		$gmap->setLatLongCenter(array(
			'200',
			'4',
			$this->owner->getLatitude(),
			$this->owner->getLongitude()
		));
		
		return $gmap;
	}
	
	public function StaticMap($width = null, $height = null) {
		$w = $width ? $width : GoogleMapUtil::$map_width;
		$h = $height ? $height : GoogleMapUtil::$map_height;

		$lat = $this->owner->getLatitude();
		$lng = $this->owner->getLongitude();

		$src = htmlentities("http://maps.google.com/maps/api/staticmap?center=$lat,$lng&markers=$lat,$lng&zoom=13&size=${w}x$h&sensor=false");

		return '<img src="'.$src.'" width="'.$w.'" height="'.$h.'" alt="'.$this->owner->Title.'" />';

	}

}
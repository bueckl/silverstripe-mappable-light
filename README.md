# Mappable Light

_A fork from Uncle Cheese's [Mappable module](https://github.com/unclecheese/Mappable) module, intended to be lightweight and only offer backend code._


**NOTE:** This is under active development.


## Getting started

Implement the `Mappable` interface, example:

	class MemberProfile extends DataObject implements Mappable {

	    static $db = array (
	        'Lat' => 'Varchar',
	        'Lon' => 'Varchar'
	        'Type' => "Enum('Farm,Restaurant')"
	    );

	/* Mappable interface requirements */

	    public function getLatitude() {
	        return $this->Lat;
	    }

	    public function getLongitude() {
	        return $this->Lon;
	    }

	    public function getMapContent() {
	        return GoogleMapUtil::sanitize($this->renderWith('MapBubbleMember'));
	    }

	    public function getMapCategory() {
	        return $this->Type;
	    }

	    public function getMapPin() {
	        return $this->Type."_pin.png";
	    }

	/* end Mappable interface */

	}

Read more on [Uncle Cheese's blog](http://www.leftandmain.com/silverstripe-tutorials/2011/06/14/new-mappable-module-and-some-unsolicited-programming-pedagogy/)



## Resources

Other noticeable Mappable forks:    
_For all, see <https://github.com/unclecheese/Mappable/network>_

* <https://github.com/unclecheese/Mappable/tree/v3>
* <https://github.com/gordonbanderson/Mappable>
	* <https://github.com/gordonbanderson/Mappable/tree/dev30>

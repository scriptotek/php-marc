<?php

namespace Scriptotek\Marc;

use Scriptotek\Marc\Fields\Location;

/**
 * Holdings record
 *
 * @property Location[] locations
 * @property Location location
 */
class HoldingsRecord extends Record
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public $properties = ['id', 'type', 'location'];

    /*************************************************************************
     * Helper methods for specific fields. Each of these are supported by
     * a class in src/Fields/
     *************************************************************************/

    /**
     * Get an array of the 852 fields as `Location` objects.
     *
     * @return Location[]
     */
    public function getLocations()
    {
        return Location::get($this);
    }

    /**
     * Get the first 852 field as a `Location` object.
     *
     * @return Location
     */
    public function getLocation()
    {
        $locations = $this->getLocations();

        return count($locations) ? $locations[0] : null;
    }
}

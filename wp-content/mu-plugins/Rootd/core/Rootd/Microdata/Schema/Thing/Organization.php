<?php

/**
 * Rootd microdata: organization schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Thing_Organization extends Rootd_Microdata_Schema_Thing
{

	protected $_itemType = 'http://schema.org/Organization';

	/**
	 * Get schema properties.
	 * 
	 * @return array
	 */
	public function getProperties()
    {
    	// Include inherited properties from parent Thing
        return array_merge(parent::getProperties(), array(
        	'address' 				=> 'Thing/Intangible/StructuredValue/ContactPoint/PostalAddress',
			'aggregateRating' 		=> 'Thing/Intangible/Rating/AggregateRating',
			'brand' 				=> 'Thing/Intangible/Brand',
			'contactPoint'			=> 'Thing/Intangible/StructuredValue/ContactPoint',
			'department'			=> 'Thing/Organization',
			'dissolutionDate'		=> 'DataType/Date',
			'duns'					=> 'DataType/Text',
			'email'					=> 'DataType/Text',
			'employee'				=> 'Thing/Person',
			'event' 				=> 'Thing/Event',
			'faxNumber'				=> 'DataType/Text',
			'founder'				=> 'Thing/Person',
			'foundingDate'			=> 'DataType/Date',
			'globalLocationNumber' 	=> 'DataType/Text',
			'hasPOS' 				=> 'Thing/Place',
			'interactionCount' 		=> 'DataType/Text',
			'isicV4'				=> 'DataType/Text',
			'legalName' 			=> 'DataType/Text',
			'location'				=> 'Thing/Intangible/StructuredValue/ContactPoint/PostalAddress',
			'logo'					=> 'Thing/Intangible/MediaObject/ImageObject',
			'makesOffer'			=> 'Thing/Intangible/Offer',
			'member'				=> 'Thing/Person',
			'memberOf'				=> 'Thing/Organization',
			'naics'					=> 'DataType/Text',
			'owns' 					=> 'Thing/Intangible/StructuredValue/OwnershipInfo',
			'review' 				=> 'Thing/CreativeWork/Review',
			'seeks'					=> 'Thing/Intangible/Demand',
			'subOrganization'		=> 'Thing/Organization',
			'taxID' 				=> 'DataType/Text',
			'telephone' 			=> 'DataType/Text',
			'vatID'					=> 'DataType/Text'
    	));
    }

}
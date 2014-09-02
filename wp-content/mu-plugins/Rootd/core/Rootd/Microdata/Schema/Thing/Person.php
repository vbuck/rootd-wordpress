<?php

/**
 * Rootd microdata: person schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Thing_Person extends Rootd_Microdata_Schema_Thing
{

	protected $_itemType = 'http://schema.org/Person';

    /**
     * Get schema properties.
     * 
     * @return array
     */
	public function getProperties()
    {
        // Include inherited properties from parent Thing
        return array_merge(parent::getProperties(), array(
        	'additionalName' 		=> 'DataType/Text',
        	'address' 				=> 'Thing/Intangible/StructuredValue/ContactPoint/PostalAddress',
        	'affiliation' 			=> 'Thing/Organization',
        	'alumniOf' 				=> 'Thing/Organization/EducationalOrganization',
        	'award' 				=> 'DataType/Text',
        	'birthDate' 			=> 'DataType/Date',
        	'brand' 				=> 'Thing/Intangible/Brand',
        	'children' 				=> 'Thing/Person',
        	'colleague' 			=> 'Thing/Person',
        	'contactPoint'			=> 'Thing/Intangible/StructuredValue/ContactPoint',
        	'deathDate' 			=> 'DataType/Date',
        	'duns' 					=> 'DataType/Text',
        	'email' 				=> 'DataType/Text',
        	'familyName' 			=> 'DataType/Text',
        	'faxNumber'				=> 'DataType/Text',
        	'follows' 				=> 'Thing/Person',
        	'gender' 				=> 'DataType/Text',
        	'givenName' 			=> 'DataType/Text',
        	'globalLocationNumber' 	=> 'DataType/Text',
        	'hasPOS' 				=> 'Thing/Place',
        	'homeLocation' 			=> 'Thing/Intangible/StructuredValue/ContactPoint',
        	'honorificPrefix' 		=> 'DataType/Text',
        	'honorificSuffic' 		=> 'DataType/Text',
        	'interactionCount' 		=> 'DataType/Text',
        	'isicV4' 				=> 'DataType/Text',
        	'jobTitle' 				=> 'DataType/Text',
        	'knows' 				=> 'Thing/Person',
        	'makesOffer' 			=> 'Thing/Intangible/Offer',
        	'memberOf' 				=> 'Thing/Organization',
        	'naics' 				=> 'DataType/Text',
        	'nationality' 			=> 'Thing/Place/AdministrativeArea/Country',
        	'owns' 					=> 'Thing/Intangible/StructuredValue/Ownershipinfo',
        	'parent' 				=> 'Thing/Person',
        	'performerIn'			=> 'Thing/Event',
        	'relatedTo' 			=> 'Thing/Person',
        	'seeks' 				=> 'Thing/Intangible/Demand',
        	'sibling' 				=> 'Thing/Person',
        	'taxID' 				=> 'DataType/Text',
        	'telephone' 			=> 'DataType/Text',
        	'vatID' 				=> 'DataType/Text',
        	'workLocation' 			=> 'Thing/Intangible/StructuredValue/ContactPoint',
        	'worksFor'				=> 'Thing/Organization',
    	));
    }

}
<?php

/**
 * Rootd microdata: creativework schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Thing_Creativework extends Rootd_Microdata_Schema_Thing
{

	protected $_itemType = 'http://schema.org/CreativeWork';

	/**
	 * Get schema properties.
	 * 
	 * @return array
	 */
	public function getProperties()
    {
    	// Include inherited properties from parent Thing
        return array_merge(parent::getProperties(), array(
        	'about' 				=> 'Thing',
			'accessibilityAPI' 		=> 'DataType/Text',
			'accessibilityControl'	=> 'DataType/Text',
			'accessibilityFeature' 	=> 'DataType/Text',
			'accessibilityHazard'	=> 'DataType/Text',
			'accountablePerson'		=> 'Thing/Person',
			'aggregateRating' 		=> 'Thing/Intangible/Rating/AggregateRating',
			'alternativeHeadline'	=> 'DataType/Text',
			'associatedMedia' 		=> 'Thing/CreativeWork/MediaObject',
			'audience' 				=> 'Thing/Intangible/Audience',
			'audio' 				=> 'Thing/CreativeWork/MediaObject/AudioObject',
			'author' 				=> 'Thing/Person',
			'award' 				=> 'DataType/Text',
			'citation' 				=> 'DataType/Text',
			'comment' 				=> 'Thing/Event/UserInteraction/UserComments',
			'commentCount'			=> 'DataType/Number',
			'contentLocation'		=> 'Thing/Place',
			'contentRating' 		=> 'DataType/Text',
			'contributor' 			=> 'Thing/Person',
			'copyrightHolder' 		=> 'Thing/Organization',
			'copyrightYear' 		=> 'DataType/Number',
			'creator' 				=> 'Thing/Person',
			'dateCreated' 			=> 'DataType/Date',
			'dateModified' 			=> 'DataType/Date',
			'datePublished' 		=> 'DataType/Date',
			'discussionUrl' 		=> 'DataType/Text/Url',
			'editor' 				=> 'Thing/Person',
			'educationalAlignment' 	=> 'Thing/Intangible/AlignmentObject',
			'educationalUse'		=> 'DataType/Text',
			'encoding' 				=> 'Thing/CreativeWork/MediaObject',
			'genre' 				=> 'DataType/Text',
			'headline' 				=> 'DataType/Text',
			'inLanguage' 			=> 'DataType/Text',
			'interactionCount' 		=> 'DataType/Text',
			'interactivityType' 	=> 'DataType/Text',
			'isBasedOnUrl' 			=> 'DataType/Text/Url',
			'isFamilyFriendly' 		=> 'DataType/Boolean',
			'keywords' 				=> 'DataType/Text',
			'learningResourceType' 	=> 'DataType/Text',
			'license' 				=> 'DataType/Text/Url',
			'mentions' 				=> 'Thing',
			'offers' 				=> 'Thing/Intangible/Offer',
			'provider' 				=> 'Thing/Person',
			'publisher' 			=> 'Thing/Organization',
			'publishingPrinciples'	=> 'DataType/Text/Url',
			'review' 				=> 'Thing/CreativeWork/Review',
			'sourceOrganization' 	=> 'Thing/Organization',
			'text' 					=> 'DataType/Text',
			'thumbnailUrl' 			=> 'DataType/Text/Url',
			'timeRequired' 			=> 'Thing/Intangible/Quantity/Duration',
			'typicalAgeRange' 		=> 'DataType/Text',
			'version' 				=> 'DataType/Number',
			'video'					=> 'Thing/CreativeWork/MediaObject/VideoObject',
    	));
    }

}
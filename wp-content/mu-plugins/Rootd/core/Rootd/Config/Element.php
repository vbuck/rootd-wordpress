<?php

/**
 * Rootd Framework configuration element class.
 *
 * Work based on Varien_Simplexml_Element for Magento.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Config_Element extends SimpleXMLElement
{

	/**
	 * Descend a node by path.
	 * 
	 * @param  	string $path
	 * @return 	mixed
	 */
	public function descend($path)
    {
        if(is_array($path)) 
        {
            $pathParts = $path;
        } 
        else 
        {
            if(strpos($path, '@') === false) 
            {
                $pathParts = explode('/', $path);
            }
            else 
            {
                $regex = "#([^@/\\\"]+(?:@[^=/]+=(?:\\\"[^\\\"]*\\\"|[^/]*))?)/?#";
                $pathParts = 
                	$pathMatches = array();

                if(preg_match_all($regex, $path, $pathMatches)) 
                {
                    $pathParts = $pathMatches[1];
                }
            }
        }

        $toDescend = $this;

        foreach($pathParts as $nodeName) 
        {
            if(strpos($nodeName, '@') !== false) 
            {
                $a 				= explode('@', $nodeName);
                $b 				= explode('=', $a[1]);
                $nodeName 		= $a[0];
                $attributeName 	= $b[0];
                $attributeValue = $b[1];
                $attributeValue = trim($attributeValue, '"');
                $found 			= false;

                foreach($toDescend->$nodeName as $subDescendant) {
                    if((string) $subDescendant[$attributeName] === $attributeValue) 
                    {
                        $found 		= true;
                        $toDescend 	= $subDescendant;

                        break;
                    }
                }

                if(!$found) 
                {
                    $toDescend = false;
                }
            } 
            else 
            {
                $toDescend = $toDescend->$nodeName;
            }

            if(!$toDescend) 
            {
                return false;
            }
        }

        return $toDescend;
    }

    /**
     * Extend a node.
     * 
     * @param  Rootd_Config_Element $source
     * @param  boolean $overwrite
     * @return Rootd_Config_Element
     */
    public function extend($source, $overwrite = false)
    {
        if(!$source instanceof SimpleXMLElement) 
        {
            return $this;
        }

        foreach($source->children() as $child) 
        {
            $this->extendChild($child, $overwrite);
        }

        return $this;
    }

    /**
     * Extend a child node.
     * 
     * @param  	Rootd_Config_Element $source
     * @param  	boolean $overwrite
     * @return 	Rootd_Config_Element
     */
    public function extendChild($source, $overwrite = false)
    {
        $targetChild 	= null;
        $sourceName 	= $source->getName();
        $sourceChildren = $source->children();

        if(!$source->hasChildren()) 
        {
            if(isset($this->$sourceName)) 
            {
                if($this->$sourceName->children()) 
                {
                    return $this;
                }
                if($overwrite) 
                {
                    unset($this->$sourceName);
                } 
                else 
                {
                    return $this;
                }
            }

            $targetChild = $this->addChild($sourceName, $source->xmlEntities());
            //$targetChild->setParent($this);

            foreach($source->attributes() as $key => $value) 
            {
                $targetChild->addAttribute($key, $this->xmlEntities($value));
            }

            return $this;
        }

        if(isset($this->$sourceName)) 
        {
            $targetChild = $this->$sourceName;
        }

        if(is_null($targetChild)) 
        {
            $targetChild = $this->addChild($sourceName);
            //$targetChild->setParent($this);

            foreach ($source->attributes() as $key => $value) 
            {
                $targetChild->addAttribute($key, $this->xmlEntities($value));
            }
        }

        foreach($sourceChildren as $childKey => $childNode) {
            $targetChild->extendChild($childNode, $overwrite);
        }

        return $this;
    }

    /**
     * Check if this node has children.
     * 
     * @return boolean
     */
    public function hasChildren()
    {
        if(!$this->children()) 
        {
            return false;
        }

        // simplexml bug: @attributes is in children() but invisible in foreach
        foreach($this->children() as $k => $child) 
        {
            return true;
        }

        return false;
    }

    /**
     * Convert meaninful XML entities.
     * 
     * @param  mixed $value
     * @return string
     */
    public function xmlEntities($value = null)
    {
        if(is_null($value)) 
        {
            $value = $this;
        }

        $value = (string) $value;
        $value = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $value);

        return $value;
    }

}
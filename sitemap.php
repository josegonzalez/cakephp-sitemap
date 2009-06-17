<?php
/**
 * Allows one to easily build a sitemap
 *
 * @author Jose Diaz-Gonzalez
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://josediazgonzalez/code/sitemaphelper/
 * @package app
 * @subpackage app.views.helpers
 * @version .4
 */
class SitemapHelper extends AppHelper {
	
	/**
	 * Uses the XML helper to return the proper header
	 *
	 * @return string XML header
	 * @author Jose Diaz-Gonzalez
	 **/
	function header() {
		App::import('Core', 'Helper'); 
		App::import('Helper', 'Xml');
		$xml  = new XmlHelper();
		return $xml->header();
	}

	/**
	 * Opens the index tag
	 *
	 * @param boolean $index set to false if this item is not for a sitemap index, true otherwise
	 * @param array $options various options pertaining to the schema, including an array of schema extensions
	 * @return string opening urlset entity
	 * @author Jose Diaz-Gonzalez
	 **/
	function openIndex($index = false, $options = array()) {
		$options = array_merge(array(
			'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
			'schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
			'url' => 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
			'extensions' => array(),
			'allExtensions' => ''),
			$options);
		$openTag = "urlset";
		if ($index) {
			$options['url'] = 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd';
			$openTag = "sitemapindex";
		}
		foreach ($options['extensions'] as $extension){
			$options['allExtensions'] .= "xmlns=\"$extension ";
		}
		return "<$openTag xmlns:xsi=\"" . $options['xsi'] . ""\"
			xsi:schemaLocation=\"" . $options['schemaLocation'] . "\"
			url=\"" . $options['url'] . "\"
			xmlns=\"" . $options['xmlns'] . "\"" .
			$options['allExtensions'] . ">";
	}

	/**
	 * Closes index tag
	 *
	 * @param boolean $index set to false if this item is not for a sitemap index, true otherwise
	 * @return string Closing tag
	 * @author Jose Diaz-Gonzalez
	 **/
	function closeIndex($index = false){
		if ($index) {
			return "</sitemapindex>";
		}
		return "</urlset>";
	}
	
	/**
	 * Creates an item for the sitemap or sitemap index
	 *
	 * @param boolean $index set to false if this item is not for a sitemap index, true otherwise
	 * @param array $options various options pertaining to the item
	 * @return string The item
	 * @author Jose Diaz-Gonzalez
	 **/
	function item($index = false, $options = array()) {
		$options = array_merge(array(
			'loc' => NULL,
			'lastmod' => NULL,
			'changefreq' => NULL,
			'priority' => NULL,
			'encode' => true
			), $options);
		if (!empty($options['loc'])) {
			if (!empty($item['lastmod'])) {
				App::import('Core', 'Helper'); 
				App::import('Helper', 'Time');
				$time  = new TimeHelper();
				$options['lastmod'] = $time->toAtom($options['lastmod']);
			}
			if ($options['encode']){
				$options['loc'] = $this->_xmlspecialchars($options['loc']);
			}
			if ($index) {
				//Construct a sitemapindex item
				$item = array();
				$item['openEntity'] = "<sitemap>";
				$item['loc'] = $this->entityMaker("loc", $options['loc']);
				$item['lastmod'] = $this->_entityMaker("lastmod", $options['lastmod']);
				$item['closeEntity'] = "</sitemap>";
				return $this->_mergeArrayEntities($item);
			} else {
				//Construct a sitemap item
				$item = array();
				$item['openEntity'] = "<url>";
				$item['loc'] = $this->entityMaker("loc", $options['loc']);
				$item['lastmod'] = $this->entityMaker("lastmod", $options['lastmod']);
				$item['changefreq'] = $this->entityMaker("changefreq", $options['lastmod']);
				$item['priority'] = $this->entityMaker("priority", $options['lastmod']);
				$item['closeEntity'] = "</url>";
				return $this->_mergeArrayEntities($item);
			}
		}
		return false;
	}
	
	function _entityMaker($entity = NULL, $string = NULL) {
		if (!empty($string)){
			return "<$entity>$string</$entity>";
		}
	}
	
	function _mergeArrayEntities($entityArray = array()) {
		$mergedArray = '';
		foreach ($entityArray as $entity) {
			$mergedArray .= $entity;
		}
		return $mergedArray;
	}
	
	function _xmlspecialchars($text) {
	   return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
	}
}
?>

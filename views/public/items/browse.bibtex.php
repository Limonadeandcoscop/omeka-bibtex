<?php

// Initialize library
$bibtex  = new Structures_BibTex();

foreach($items as $item)  {

	$values = array();

	// Retrieve elements texts
	$elements = all_element_texts($item, array('return_type' => 'array'));

	// Get mapping from ini file
	$ini 		= new Zend_Config_Ini(BIBTEXT_PLUGIN_DIR . '/mapping.ini');
	$mapping 	= $ini->fields->toArray();

	$values['entryType'] = 'Book';
	$values['cite']      = trim(get_option('site_title'));

	foreach ($elements as $elementSetName => $elementTexts) {

		foreach ($elementTexts as $elementName => $elementsText) {

			if ($bibtexElementName = @$mapping[$elementName]) {

				if (count($elementsText) > 1) {
					foreach ($elementsText as $element) {
						if (strlen(trim($element)))
							@$values[$bibtexElementName] .= ', '.strip_tags(str_replace('&#039;', "'", $element));
					}
				} else {
					if (strlen(trim(@$elementsText[0])))
						$values[$bibtexElementName] =  strip_tags(str_replace('&#039;', "'", $elementsText[0]));
				}

				if (isset($values[$bibtexElementName]))
					$values[$bibtexElementName] = preg_replace('/^, /', '', $values[$bibtexElementName]);
			}
		}
	}

	// Add creator
	$author = metadata($item, array('Dublin Core', 'Creator'), array('delimiter' => ' - '));
	$values['author'] = array(array("first" => "", "last" => strip_tags($author)));
	$bibtex->addEntry($values);
}

print $bibtex->bibTex();


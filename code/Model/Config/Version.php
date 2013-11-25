<?php
class CosmoCommerce_Updates_Model_Config_Version
{
	private $gfonts = array(
        '΢���ź�','����','����'
    );

    public function toOptionArray()
    {
	    $options = array();
	    foreach ($this->gfonts as $f ){
		    $options[] = array(
			    'value' => $f,
			    'label' => $f,
		    );
	    }

        return $options;
    }

}

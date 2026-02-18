<?php
  namespace Kraftors\Updateproduct\Controller\Index;
 
  class Updatesku extends \Magento\Framework\App\Action\Action
  {	

    	protected $_fileCsv;

	protected $_moduleReader;

	public function __construct(
	   \Magento\Backend\App\Action\Context $context,
	   \Magento\Framework\Module\Dir\Reader $moduleReader,
	   \Magento\Framework\File\Csv $fileCsv
	) {
	    $this->_moduleReader = $moduleReader;
	    $this->_fileCsv = $fileCsv;
	    parent::__construct($context); // If your class doesn't have a parent, you don't need to do this, of course.
	}
     
	public function execute()
	{
		echo 'My Action';
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_product = $objectManager->get('Magento\Catalog\Model\Product')->load(2100);

		echo "<br/>";
		echo $_product->getName();
		echo "<br/>";
		$_children = $_product->getTypeInstance()->getUsedProducts($_product);
		echo count($_children);
		echo "<br/>";
		// This is the directory where you put your CSV file.
		$directory = $this->_moduleReader->getModuleDir('etc', 'Kraftors_Updateproduct'); 

		// This is your CSV file.
		$file = $directory . '/acuvue_moist_30.csv';

		/*if (file_exists($file)) {
		    $data = $this->_fileCsv->getData($file);
		    // This skips the first line of your csv file, since it will probably be a heading. Set $i = 0 to not skip the first line. 
		    for($i=1; $i<count($data); $i++) {
			var_dump($data[$i]); // $data[$i] is an array with your csv columns as values.
			echo "<br/>";
			echo $spower = $data[$i][12];
			echo $sbasecurve = $data[$i][13];
			echo $sdiameter = $data[$i][14];
			echo "<br/>";


		    }
		}*/

		foreach ($_children as $child){
		    	$productId = $child->getID();
			echo $productId;
			echo "<br/>";
			/* Get Entitiy Resolver*/
			$entryResolver = $objectManager->get('Magento\Catalog\Model\Product\Gallery\EntryResolver');
			/* Get Product Object */
			$product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
			echo "<br/>";
			echo $sku = $product->getSku();
			//echo "<pre>";

			echo $power = $product->getResource()->getAttribute('power')->getFrontend()->getValue($product);
			echo "<br/>";

			echo $diameter = $product->getResource()->getAttribute('diameter')->getFrontend()->getValue($product);
			echo "<br/>";

			echo $basecurve = $product->getResource()->getAttribute('basecurve')->getFrontend()->getValue($product);
			echo "<br/>";

			if (file_exists($file)) {
			    $data = $this->_fileCsv->getData($file);
			    // This skips the first line of your csv file, since it will probably be a heading. Set $i = 0 to not skip the first line. 
			    for($i=1; $i<count($data); $i++) {
				//var_dump($data[$i]); // $data[$i] is an array with your csv columns as values.
				echo "<br/>";
				
				echo $spower = $data[$i][12];
				echo $sbasecurve = $data[$i][13];
				echo $sdiameter = $data[$i][14];
				echo $ssku = $data[$i][0];
				echo "<br/>";
				if($power == $spower && $basecurve == $sbasecurve && $diameter == $sdiameter && $sku != $ssku){
					echo "Inside If";
					echo "<br/>";
					$product->setSku($ssku);
					$product->save();
						
				}

			    }
			}

		}
	} 
  }


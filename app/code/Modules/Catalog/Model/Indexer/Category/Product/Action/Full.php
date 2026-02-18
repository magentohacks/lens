<?php 
namespace Modules\Catalog\Model\Indexer\Category\Product\Action;

/**
 * Class AbstractAction
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
*/

class Full extends \Magento\Catalog\Model\Indexer\Category\Product\Action\Full {

    public function isRangingNeeded() {
        return false; //It was "true" on default vendor part. 
    }
}
?>
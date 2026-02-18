<?php
namespace Lens\Manager\Controller\Adminhtml\Prescriptions;

use Magento\Backend\App\Action\Context;
use Lens\Manager\Model\LensPrescriptionsFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param BlockRepository $blockRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        LensPrescriptionsFactory $lensPrescriptions,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->lensPrescriptions = $lensPrescriptions;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $prescriptionsId) {
                    /** @var \Magento\Cms\Model\Block $block */
                    $prescriptions = $this->lensPrescriptions->create()->load($prescriptionsId);
                    try {
                        $prescriptions->setData(array_merge($prescriptions->getData(), $postItems[$prescriptionsId]))->save();
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}

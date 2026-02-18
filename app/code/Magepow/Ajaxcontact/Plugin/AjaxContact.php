<?php
namespace Magepow\Ajaxcontact\Plugin;

class AjaxContact
{
   
    protected $_messageManager;

   /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $_dataPersistor;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $_redirect;

    /**
     * PostPlugin constructor.
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_dataPersistor = $dataPersistor;
        $this->_redirect = $redirect;
        $this->_messageManager = $messageManager;
    }

    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {
        if ($subject->getRequest()->isAjax()) 
        {
            $message = $this->_messageManager->getMessages()->getLastAddedMessage();
            // echo $message->toString();
            $response = [
                'type' => $message->getType(),
                'text' => $message->getText(),
            ];
            /**
             * clear messages when refresh site.
             */
            $this->_messageManager->getMessages()->clear();
            $response = json_encode($response);
            $subject->getResponse()->setBody( $response );

            return;

        }
        
        return $result;
    }

}

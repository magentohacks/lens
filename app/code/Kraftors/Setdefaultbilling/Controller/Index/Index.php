<?php
  namespace Kraftors\Setdefaultbilling\Controller\Index;

  class Index extends \Magento\Framework\App\Action\Action
  {

	protected $order;

  protected $_orderCollectionFactory;

  /**
 * @var \Magento\Customer\Api\AddressRepositoryInterface
 */
  protected $_addressFactory;

  protected $_customerFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
	)
    {
 	      $this->order = $order;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressRepository;
        return parent::__construct($context);
    }

    public function execute()
    {

      $address = $this->_addressFactory->create();
          //get customer model before you can get its address data
      $customer = $customerFactory->create()->load(1);    //insert customer id

      $order = $this->order->load(29880); // Order Id from which billing address to be taken
      $billingAddress = $order->getBillingAddress();

      //billing
      $billingAddressId = $customer->getDefaultBilling();

        if($billingAddressId == '' || $billingAddressId == 0)
        {

              $address->setCustomerId($customer->getId())
              ->setFirstname('Mav')
              ->setLastname('rick')
              ->setCountryId('HR')
              //->setRegionId('1') //state/province, only needed if the country is USA
              ->setPostcode('31000')
              ->setCity('Osijek')
              ->setTelephone('0038511223344')
              ->setFax('0038511223355')
              ->setCompany('GMI')
              ->setStreet('NO:12 Lake View')
              ->setIsDefaultBilling('1')
              ->setIsDefaultShipping('1')
              ->setSaveInAddressBook('1');
              try{
                      $address->save();
              }
              catch (Exception $e) {
                      Zend_Debug::dump($e->getMessage());
              }


        }
	  }
  }


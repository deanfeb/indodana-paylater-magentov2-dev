<?php

namespace Indodana\PayLater\Controller\Index;
use Indodana\PayLater\Helper\Transaction;

class paymentoptions extends \Magento\Framework\App\Action\Action
{
    protected $_resultFactory;
    protected $_transaction;
    protected $_request;
    
    public function __construct
    (
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\Action\Context $context,
        Transaction $transaction,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_resultFactory = $jsonResultFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        
        return parent::__construct($context);
    }

    public function execute(){
        $result = $this->_resultFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');         
        $Installment=$this->_transaction->getInstallmentOptions($cart->getQuote());
        $passMinAmount = $this->_transaction->getMinimumTotalAmount() < $this->_transaction->getTotalAmount($cart->getQuote());
        $products = $this->_transaction->getProducts($cart->getQuote());
        $passMaxPrice =true;
        foreach($products as $product) {
            if($product['price'] > 25000000){
                $passMaxPrice=false;                    
            }
        }
        return $result->setData(
            [
                'Installment' => $Installment,
                'OrderID' => $cart->getQuote()->getId(),
                'CurCode' => $this->_transaction->getOrderCurrencyCode($cart->getQuote()),
                'PassMinAmount' => $passMinAmount ,
                'PassMaxItemPrice' => $passMaxPrice
            ]
            );    
    }
}
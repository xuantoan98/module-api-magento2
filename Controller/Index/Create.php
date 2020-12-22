<?php

namespace AHT\Blog\Controller\Index;

use \Magento\Framework\App\Action\Action;

class Create extends Action
{
	protected $_pageFactory;

	protected $_postFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\AHT\Blog\Model\PostFactory $postFactory
		)
	{
		$this->_pageFactory = $pageFactory;
		$this->_postFactory = $postFactory;
		
		return parent::__construct($context);
	}

	public function execute()
	{
		return $this->_pageFactory->create();
	}
}
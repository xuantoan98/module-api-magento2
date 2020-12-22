<?php

namespace AHT\Blog\Block;

use \Magento\Framework\View\Element\Template;

class Create extends Template
{
	private $postFactory;
	private $postRepository;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
		\AHT\Blog\Model\PostFactory $postFactory, 
		\AHT\Blog\Model\PostRepository $postRepository
    )
	{
		parent::__construct($context);
		$this->postFactory = $postFactory;
		$this->postRepository = $postRepository;
	}
}

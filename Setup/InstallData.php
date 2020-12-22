<?php

namespace AHT\Setup\Blog;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface
{
    protected $_postFactory;

    public function __construct(\AHT\Blog\Model\PostFactory $postFactory)
    {
        $this->_postFactory = $postFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            'name' => "How to Create SQL Setup Script in Magento 2"
        ];
        $post  = $this->_postFactory->create();
        $post->addData($post)->save();
    }
}

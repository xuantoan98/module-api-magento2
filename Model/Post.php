<?php

namespace AHT\Blog\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use AHT\Blog\Api\Data\PostInterface;

// class Post extends AbstractModel implements IdentityInterface
class Post extends AbstractModel implements IdentityInterface, PostInterface
{
    const CACHE_TAG = 'aht_blog_post';

    protected $_cacheTag = 'aht_blog_post';

    protected $_eventPrefix = 'aht_blog_post';

    protected function _construct()
    {
        $this->_init('AHT\Blog\Model\ResourceModel\Post');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

		return $values;
    }

    public function getPostId()
    {
        return $this->getData('post_id');
    }

    public function setPostId($postId)
    {
        $this->getData('post_id', $postId);
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($name)
    {
        $this->setData('name', $name);
    }
}


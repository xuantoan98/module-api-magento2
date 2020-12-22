<?php

namespace AHT\Blog\Model;

use AHT\Blog\Api\Data;
use AHT\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use AHT\Blog\Model\ResourceModel\Post as ResourcePost;
use AHT\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use AHT\Blog\Api\Data\PostInterface;


class PostRepository implements PostRepositoryInterface
{
    protected $resource;

    protected $PostFactory;
    
    protected $PostCollectionFactory;

    protected $searchResultsFactory;

    private $collectionProcessor;

    public function __construct(
        ResourcePost $resource,
        PostFactory $PostFactory,
        Data\PostInterfaceFactory $dataPostFactory,
        PostCollectionFactory $PostCollectionFactory
    )
    {
        $this->resource = $resource;
        $this->PostFactory = $PostFactory;
        $this->PostCollectionFactory = $PostCollectionFactory;
    }

    /**
     * Undocumented function
     *
     * @param \AHT\Blog\Api\Data\PostInterface $Post
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function save(\AHT\Blog\Api\Data\PostInterface $Post)
    {
        try {
            $this->resource->save($Post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Post: %1', $exception->getMessage()),
                $exception
            );
        }
        return $Post;
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return \AHT\Blog\Model\ResourceModel\Post
     */
    public function getById($id)
    {
        $postId = intval($id);
        $Post = $this->PostFactory->create();
        $Post->load($postId);
        if (!$Post->getId()) {
            throw new NoSuchEntityException(__('The CMS Post with the "%1" ID doesn\'t exist.', $postId));
        }
        $result = $Post->toArray();

        return json_encode($result);
        // return $Post->getData();
    }

        /**
     * Undocumented function
     *
     * @param [type] $id
     * @return \AHT\Blog\Model\ResourceModel\Post
     */
    public function getPostById($id)
    {
        $postId = intval($id);
        $Post = $this->PostFactory->create();
        $Post->load($postId);

        if (!$Post->getId()) {
            throw new NoSuchEntityException(__('The CMS Post with the "%1" ID doesn\'t exist.', $postId));
        }

        return $Post;
    }

    /**
     * function get all data
     *
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function getList()
    {
        $collection = $this->PostCollectionFactory->create();
        return $collection->getData();
    }

    /**
     * @inheritdoc
     *  
     * @return \AHT\Blog\Api\Data\PostInterface
     * 
     * @throws LocalizedException
     */
    public function createPost(
        PostInterface $post
    )
    {
        try {
            $this->resource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Post: %1', $exception->getMessage()),
                $exception
            );
        }
        return json_encode(array(
            "status" => 200,
            "message" => $post->getData()
        ));
    }

    public function updatePost(String $postId, PostInterface $post)
    {
        try {
            $objPost = $this->PostFactory->create();
            $id = intval($postId);
            $objPost->setId($id);
            $objPost->setName($post->getName());

            $this->resource->save($objPost);

            return $objPost->getData();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Post: %1', $exception->getMessage()),
                $exception
            );
        }
    }

    public function deletePostById($postId)
    {
        $id = intval($postId);
        if($this->resource->delete($this->getPostById($id))) {
            return json_encode([
                "status" => 200,
                "message" => "Successfully"
            ]);
        }
        
    }
}


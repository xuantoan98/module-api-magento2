<?php

namespace AHT\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PostRepositoryInterface
{
    /**
     * Function save data into database
     *
     * @param \AHT\Blog\Api\Data\PostInterface $Post
     * 
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function save(\AHT\Blog\Api\Data\PostInterface $Post);
    
    /**
     * Get object by id
     * 
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function getById(String $id);

    /**
     * Create post.
     *
     * @param \AHT\Blog\Api\Data\PostInterface $post
     * 
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function createPost(
        \AHT\Blog\Api\Data\PostInterface $post
    );

    /**
     * Update post
     *
     * @param String $postId
     * @param \AHT\Blog\Api\Data\PostInterface $post
     * 
     * @return null
     */
    public function updatePost(String $postId, \AHT\Blog\Api\Data\PostInterface $post);

    /**
     * Delete post
     *
     * @param string $postId
     * 
     * @return \AHT\Blog\Api\Data\PostInterface
     */
    public function deletePostById($postId);
}

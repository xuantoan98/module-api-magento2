<?php

namespace AHT\Blog\Api\Data;

/**
 * Post Interface
 * @api
 * @since 2020
 */
interface PostInterface
{

    const ID = 'post_id';

    /**
     * Get post id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set post id
     *
     * @param int $id
     * @return @this
     */
    public function setId($id);

    /**
     * Get post name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set post name
     *
     * @param string $name
     * @return null
     */
    public function setName($name);


}

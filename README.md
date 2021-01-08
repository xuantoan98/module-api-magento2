# module-api-magento 2

## Bắt đầu ngay !

### Tạo mới module
```php
// registration.php file
<?php
\Magento\Framework\Component\ComponentRegistrar::register(
	\Magento\Framework\Component\ComponentRegistrar::MODULE,
	'AHT_Blog',
	__DIR__
);
```

### Tạo bảng xử lý dữ liệu
```php
// [Vendor/MyModule/Setup]/InstallSchema.php
<?php

namespace AHT\Blog\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('aht_blog_post'))
        {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('aht_blog_post')
            )
                ->addColumn(
                    'post_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
                )
                ->addColumn(
                    'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Name'
                )
                ->setComment('Blog Post Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('aht_blog_post'),
                $setup->getIdxName(
                    $installer->getTable('aht_blog_post'),
                    ['name'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
```

### Tạo route api
```xml
 <!-- [Vendor/MyModule/etc]/webapi.xml -->
<?xml version="1.0" ?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">
	<route method="GET" url="/V1/getById/:id">
		<service class="AHT\Blog\Api\PostRepositoryInterface" method="getById"/>
		<resources>
            <resource ref="anonymous"/>
        </resources>
	</route>

	<route method="GET" url="/V1/post">
		<service class="AHT\Blog\Model\PostRepository" method="getList"/>
		<resources>
            <resource ref="anonymous"/>
        </resources>
	</route>

	<route method="POST" url="/V1/post">
		<service class="AHT\Blog\Api\PostRepositoryInterface" method="createPost"/>
		<resources>
            <resource ref="anonymous"/>
        </resources>
	</route>

	<route method="PUT" url="/V1/post/:postId">
		<service class="AHT\Blog\Api\PostRepositoryInterface" method="updatePost"/>
		<resources>
            <resource ref="anonymous"/>
        </resources>
	</route>

	<route method="DELETE" url="/V1/post/:postId">
		<service class="AHT\Blog\Api\PostRepositoryInterface" method="deletePostById"/>
		<resources>
            <resource ref="anonymous"/>
        </resources>
	</route>

	<route url="/V1/customers" method="POST">
		<service class="Magento\Customer\Api\AccountManagementInterface" method="createAccount"/>
		<resources>
			<resource ref="anonymous"/>
		</resources>
	</route>
</routes>


<!-- [Vendor/MyModule/etc]/di.xml -->
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
	<preference for="AHT\Blog\Api\PostRepositoryInterface" type="AHT\Blog\Model\PostRepository"/>

	<preference for="AHT\Blog\Api\Data\PostInterface" type="AHT\Blog\Model\Post"/>
</config>
```

### Tạo Model, ResourceModel và Collection
```php
// [Vendor/MyModule/Model]/PostRepository.php
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


// [Vendor/MyModule/Model]/Post.php
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


// [Vendor/MyModule/Model/ResourceModel]/Post.php
<?php

namespace AHT\Blog\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Post extends AbstractDb
{
    public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('aht_blog_post', 'post_id');
	}
}

// [Vendor/MyModule/Model/ResourceModel/Post]/Collection.php
<?php

namespace AHT\Blog\Model\ResourceModel\Post;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'aht_blog_post_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('AHT\Blog\Model\Post', 'AHT\Blog\Model\ResourceModel\Post');
	}
}

```

### Tạo interface và repository thực thi
```php
// [Vendor/MyModule/Api]/PostRepositoryInterface.php
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

// [Vendor/MyModule/Api/Data]/PostInterface.php
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
```

### Chạy command line build and run module
```
 - php bin/magento setup:upgrade
 - php bin/magento setup:di:compile
 - php bin/magento c:c
```

### Demo module
1. GET TOKEN
    + Method: POST
    + URL: http://localhost/magento1/rest/default/V1/integration/admin/token
    + Params: 
        ```json
        {
            "username": "[username Admin Magento]",
            "password": "[password Admin Magento]"
        }
        ```
    + Response: TOKEN

2. GET
    + Method: GET
    + URL: http://localhost/magento1/rest/default/V1/post
    + Params: none
    + Response: array [];

3. POST
    + Method: POST
    + URL: http://localhost/magento1/rest/default/V1/post
    + Params: 
        ```json
        {
            "post": {
                "name": "Title Post want Create"
            }
        }
        ```
    + Response: json_encode: key status and message
4. PUT
    + Method: PUT
    + URL: http://localhost/magento1/rest/default/V1/post/:post_id
    + Params: 
        ```json
        {
            "post": {
                "name": "Title Post want Update"
            }
        }
        ```
    + Response: array [] post updated

5. DELETE
    + Method: DELETE
    + URL: http://localhost/magento1/rest/default/V1/post/:post_id
    + Params: none
    + Response: json_encode: key status and message

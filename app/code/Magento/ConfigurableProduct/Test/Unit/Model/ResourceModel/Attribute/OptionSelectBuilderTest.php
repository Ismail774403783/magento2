<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Test\Unit\Model\ResourceModel\Attribute;

use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilder;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionProvider;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class OptionSelectBuilderTest
 */
class OptionSelectBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionSelectBuilder
     */
    private $model;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeResourceMock;

    /**
     * @var OptionProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeOptionProviderMock;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var AbstractAttribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractAttributeMock;

    /**
     * @var ScopeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scope;


    protected function setUp()
    {
        $this->connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['select', 'getIfNullSql'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->select = $this->getMockBuilder(Select::class)
            ->setMethods(['from', 'joinInner', 'joinLeft', 'where'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->connectionMock->expects($this->atLeastOnce())
            ->method('select')
            ->willReturn($this->select);

        $this->attributeResourceMock = $this->getMockBuilder(Attribute::class)
            ->setMethods(['getTable', 'getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeResourceMock->expects($this->atLeastOnce())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->attributeOptionProviderMock = $this->getMockBuilder(OptionProvider::class)
            ->setMethods(['getProductEntityLinkField'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractAttributeMock = $this->getMockBuilder(AbstractAttribute::class)
            ->setMethods(['getBackendTable', 'getAttributeId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->scope = $this->getMockBuilder(ScopeInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            OptionSelectBuilder::class,
            [
                'attributeResource' => $this->attributeResourceMock,
                'attributeOptionProvider' => $this->attributeOptionProviderMock,
            ]
        );
    }

    /**
     * Test for method getSelect
     */
    public function testGetSelect()
    {
        $this->select->expects($this->any())->method('from')->willReturnSelf();
        $this->select->expects($this->any())->method('joinInner')->willReturnSelf();
        $this->select->expects($this->any())->method('joinLeft')->willReturnSelf();
        $this->select->expects($this->any())->method('where')->willReturnSelf();

        $this->abstractAttributeMock->expects($this->atLeastOnce())
            ->method('getAttributeId')
            ->willReturn('getAttributeId value');
        $this->abstractAttributeMock->expects($this->atLeastOnce())
            ->method('getBackendTable')
            ->willReturn('getMainTable value');

        $this->scope->expects($this->atLeastOnce())->method('getId')->willReturn(123);

        $this->assertEquals(
            $this->select,
            $this->model->getSelect($this->abstractAttributeMock, 4, $this->scope)
        );
    }
}

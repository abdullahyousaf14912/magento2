<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Test\Unit\Model\Sample;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Model\Product;
use Magento\Downloadable\Api\Data\SampleInterface;
use Magento\Downloadable\Api\SampleRepositoryInterface;
use Magento\Downloadable\Model\Sample\CreateHandler;
use Magento\Downloadable\Model\Product\Type;

class CreateHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateHandler */
    protected $model;

    /** @var SampleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $sampleRepositoryMock;

    protected function setUp()
    {
        $this->sampleRepositoryMock = $this->getMockBuilder(SampleRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->model = new CreateHandler(
            $this->sampleRepositoryMock
        );
    }

    public function testExecute()
    {
        $entitySku = 'sku';
        $entityStoreId = 0;

        /** @var SampleInterface|\PHPUnit_Framework_MockObject_MockObject $sampleMock */
        $sampleMock = $this->getMockBuilder(SampleInterface::class)
            ->getMock();
        $sampleMock->expects($this->once())
            ->method('setId')
            ->with(null);

        /** @var ProductExtensionInterface|\PHPUnit_Framework_MockObject_MockObject $productExtensionMock */
        $productExtensionMock = $this->getMockBuilder(ProductExtensionInterface::class)
            ->getMock();
        $productExtensionMock->expects($this->once())
            ->method('getDownloadableProductSamples')
            ->willReturn([$sampleMock]);

        /** @var Product|\PHPUnit_Framework_MockObject_MockObject $entityMock */
        $entityMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Type::TYPE_DOWNLOADABLE);
        $entityMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($productExtensionMock);
        $entityMock->expects($this->once())
            ->method('getSku')
            ->willReturn($entitySku);
        $entityMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($entityStoreId);

        $this->sampleRepositoryMock->expects($this->never())
            ->method('getList');
        $this->sampleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($entitySku, $sampleMock, !$entityStoreId);
        $this->sampleRepositoryMock->expects($this->never())
            ->method('delete');

        $this->assertEquals($entityMock, $this->model->execute($entityMock));
    }

    public function testExecuteNonDownloadable()
    {
        /** @var Product|\PHPUnit_Framework_MockObject_MockObject $entityMock */
        $entityMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Type::TYPE_DOWNLOADABLE . 'some');
        $entityMock->expects($this->never())
            ->method('getExtensionAttributes');
        $entityMock->expects($this->never())
            ->method('getSku');
        $entityMock->expects($this->never())
            ->method('getStoreId');

        $this->sampleRepositoryMock->expects($this->never())
            ->method('getList');
        $this->sampleRepositoryMock->expects($this->never())
            ->method('save');
        $this->sampleRepositoryMock->expects($this->never())
            ->method('delete');

        $this->assertEquals($entityMock, $this->model->execute($entityMock));
    }
}

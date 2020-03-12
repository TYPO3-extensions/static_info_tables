<?php

namespace SJBR\StaticInfoTables\Tests\Unit\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use SJBR\StaticInfoTables\Domain\Model\AbstractEntity;
use SJBR\StaticInfoTables\Domain\Repository\AbstractEntityRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Testcase.
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class AbstractEntityRepositoryTest extends UnitTestCase
{
    /**
     * @var AbstractEntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject = null;

    protected function setUp()
    {
        /** @var ObjectManagerInterface $objectManager */
        $objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
        $this->subject = $this->getMockForAbstractClass(
            'SJBR\\StaticInfoTables\\Domain\\Repository\\AbstractEntityRepository',
            [$objectManager]
        );
    }

    /**
     * @test
     */
    public function initializeObjectSetsRespectStoragePidToFalse()
    {
        /** @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
        $subject = $this->getMockForAbstractClass(
            'SJBR\\StaticInfoTables\\Domain\\Repository\\AbstractEntityRepository',
            [$objectManager]
        );

        $querySettings = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $objectManager->expects($this->once())
            ->method('get')
            ->with('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings')
            ->will($this->returnValue($querySettings));
        $querySettings->expects($this->once())->method('setRespectStoragePage')->with(false);

        /** @var AbstractEntityRepository $subject */
        $subject->initializeObject();
    }

    /**
     * @test
     */
    public function initializeObjectSetsDefaultQuerySettings()
    {
        /** @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
        $subject = $this->getMock(
            'SJBR\\StaticInfoTables\\Domain\\Repository\\AbstractEntityRepository',
            ['setDefaultQuerySettings'],
            [$objectManager]
        );

        $querySettings = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $objectManager->expects($this->once())
            ->method('get')
            ->with('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings')
            ->will($this->returnValue($querySettings));

        $subject->expects($this->once())->method('setDefaultQuerySettings')->with($querySettings);

        /** @var AbstractEntityRepository $subject */
        $subject->initializeObject();
    }

    /**
     * @test
     *
     * @expectedException \BadMethodCallException
     */
    public function addThrowsException()
    {
        $this->subject->add(new AbstractEntity());
    }

    /**
     * @test
     *
     * @expectedException \BadMethodCallException
     */
    public function removeThrowsException()
    {
        $this->subject->remove(new AbstractEntity());
    }

    /**
     * @test
     *
     * @expectedException \BadMethodCallException
     */
    public function updateThrowsException()
    {
        $this->subject->update(new AbstractEntity());
    }

    /**
     * @test
     *
     * @expectedException \BadMethodCallException
     */
    public function removeAllThrowsException()
    {
        $this->subject->removeAll();
    }
}

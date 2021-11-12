<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\DependencyInjection\Compiler;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\NormalizerConfigPass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NormalizerConfigPassTest extends TestCase
{
    public function testFieldsMustBeStringsOrArrays()
    {
        $this->markTestIncomplete();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The values of the "fields" option for the "edit" view of the "AppBundle\Entity\TestEntity" entity can only be strings or arrays.');
        $backendConfig = ['entities' => [
            'TestEntity' => [
                'class' => 'AppBundle\Entity\TestEntity',
                'edit' => [
                    'fields' => ['20'],
                ],
            ],
        ]];

        $configPass = new NormalizerConfigPass($this->getServiceContainer());
        $configPass->process($backendConfig);
    }

    public function testFieldsMustDefinePropertyOption()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('One of the values of the "fields" option for the "edit" view of the "AppBundle\Entity\TestEntity" entity does not define neither of the mandatory options ("property" or "type")');
        $backendConfig = ['entities' => [
            'TestEntity' => [
                'class' => 'AppBundle\Entity\TestEntity',
                'edit' => [
                    'fields' => [
                        ['label' => 'Field without "property" option'],
                    ],
                ],
            ],
        ]];

        $configPass = new NormalizerConfigPass($this->getServiceContainer());
        $configPass->process($backendConfig);
    }

    private function getServiceContainer()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Tests\Fixtures\AbstractTestCase;
use Symfony\Component\HttpKernel\Kernel;

class DisabledActionsTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->initClient(['environment' => 'disabled_actions']);
    }

    public function testAssociationLinksInListView()
    {
        if (2 === (int) Kernel::MAJOR_VERSION) {
            $this->markTestSkipped('This test is not compatible with Symfony 2.x.');
        }

        $crawler = $this->requestListView('Purchase');

        $this->assertSame(
            'user1',
            trim($crawler->filter('td[data-label="Buyer"]')->first()->html()),
            'The "buyer" field in the "list" view of the "Purchase" item does not contain a link because the "show" action is disabled for the "User" entity.'
        );
    }

    public function testAssociationLinksInShowView()
    {
        if (2 === (int) Kernel::MAJOR_VERSION) {
            $this->markTestSkipped('This test is not compatible with Symfony 2.x.');
        }

        // 'Purchase' entity 'id' is generated randomly. In order to browse the
        // 'show' view of the first 'Purchase' entity, browse the 'list' view
        // and get the 'id' from the first row of the listing
        $crawler = $this->requestListView('Purchase');
        $firstPurchaseId = trim($crawler->filter('td[data-label="ID"]')->first()->text());
        $crawler = $this->requestShowView('Purchase', $firstPurchaseId);

        $this->assertSame(
            'user1',
            trim($crawler->filter('.field-association:contains("Buyer") .form-control')->first()->html()),
            'The "buyer" field in the "show" view of the "Purchase" item does not contain a link because the "show" action is disabled for the "User" entity.'
        );
    }

    public function testAccessingDisabledActions()
    {
        $crawler = $this->requestShowView('User', 1);

        $this->assertStringContainsString(
            'The requested &quot;show&quot; action is not allowed for the &quot;User&quot; entity.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testBooleanTogglesWhenEditIsDisabled()
    {
        $crawler = $this->requestListView('Product');

        $this->assertCount(15, $crawler->filter('td[data-label="Enabled"].boolean'), 'When "edit" action is disabled, boolean properties are displayed as labels, not toggles.');
        $this->assertCount(0, $crawler->filter('td[data-label="Enabled"].toggle'));
    }

    /**
     * @dataProvider provideRedirections
     */
    public function testRedirectToDisabledActions($view, $entityName, $expectedRedirectionLocation)
    {
        $crawler = 'edit' === $view ? $this->requestEditView($entityName) : $this->requestNewView($entityName);
        $form = $crawler->selectButton('Save changes')->form([
            strtolower($entityName).'[name]' => 'New Category Name',
        ]);
        $this->client->submit($form);

        $this->assertStringContainsString($expectedRedirectionLocation, $this->client->getResponse()->headers->get('location'));
    }

    public function provideRedirections()
    {
        return [
            'Edit action: List is enabled, redirect to list' => ['edit', 'Category', '/admin/?action=list&entity=Category'],
            'Edit action: List is disabled, redirect to edit' => ['edit', 'Category2', '/admin/?action=edit&entity=Category2&id=200'],
            'New action: List is enabled, redirect to list' => ['new', 'Category', '/admin/?action=list&entity=Category'],
            'New action: List is disabled, redirect to edit' => ['new', 'Category2', '/admin/?action=edit&entity=Category2&id=201'],
            'New action: List and edit is disabled, redirect to new' => ['new', 'Category3', '/admin/?action=new&entity=Category3'],
        ];
    }
}

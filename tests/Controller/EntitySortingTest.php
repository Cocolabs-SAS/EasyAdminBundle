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

class EntitySortingTest extends AbstractTestCase
{
    public function setUp(): void
    {
        // parent::setUp();

        $this->initClient(['environment' => 'entity_sorting']);
    }

    public function testMainMenuSorting()
    {
        $crawler = $this->requestListView('Product');

        $this->assertStringContainsString('sortField=price', $crawler->filter('.sidebar-menu a:contains("Product 1")')->attr('href'));
        $this->assertStringNotContainsString('sortDirection', $crawler->filter('.sidebar-menu a:contains("Product 1")')->attr('href'));
        $this->assertStringContainsString('sortField=price', $crawler->filter('.sidebar-menu a:contains("Product 2")')->attr('href'));
        $this->assertStringContainsString('sortDirection=ASC', $crawler->filter('.sidebar-menu a:contains("Product 2")')->attr('href'));
        $this->assertStringContainsString('sortField=id', $crawler->filter('.sidebar-menu a:contains("Product 3")')->attr('href'));
        $this->assertStringNotContainsString('sortDirection', $crawler->filter('.sidebar-menu a:contains("Product 3")')->attr('href'));

        // click on any menu item to sort contents differently
        $link = $crawler->filter('.sidebar-menu a:contains("Product 2")')->link();
        $crawler = $this->client->click($link);
        $this->assertStringNotContainsString('sorted', $crawler->filter('th[data-property-name="name"]')->attr('class'));
        $this->assertStringContainsString('sorted', $crawler->filter('th[data-property-name="price"]')->attr('class'));
        $this->assertStringContainsString('fa-caret-up', $crawler->filter('th[data-property-name="price"] i')->attr('class'));
    }

    public function testListViewSorting()
    {
        $crawler = $this->requestListView('Product');

        // check the default sorting of the page
        $this->assertStringContainsString('sorted', $crawler->filter('th[data-property-name="name"]')->attr('class'));
        $this->assertStringContainsString('fa-caret-down', $crawler->filter('th[data-property-name="name"] i')->attr('class'));

        // click on any other table column to sort contents differently
        $link = $crawler->filter('th[data-property-name="price"] a')->link();
        $crawler = $this->client->click($link);
        $this->assertStringNotContainsString('sorted', $crawler->filter('th[data-property-name="name"]')->attr('class'));
        $this->assertStringContainsString('sorted', $crawler->filter('th[data-property-name="price"]')->attr('class'));
        $this->assertStringContainsString('fa-caret-down', $crawler->filter('th[data-property-name="price"] i')->attr('class'));
    }

    public function testSearchViewSorting()
    {
        $crawler = $this->requestSearchView('lorem', 'Product');

        // check the default sorting of the page
        $this->assertStringContainsString('sorted', $crawler->filter('th[data-property-name="createdAt"]')->attr('class'));
        $this->assertStringContainsString('fa-caret-up', $crawler->filter('th[data-property-name="createdAt"] i')->attr('class'));

        // click on any other table column to sort contents differently
        $link = $crawler->filter('th[data-property-name="name"] a')->link();
        $crawler = $this->client->click($link);
        $this->assertStringNotContainsString('sorted', $crawler->filter('th[data-property-name="createdAt"]')->attr('class'));
        $this->assertStringContainsString('sorted', $crawler->filter('th[data-property-name="name"]')->attr('class'));
        $this->assertStringContainsString('fa-caret-down', $crawler->filter('th[data-property-name="name"] i')->attr('class'));
    }
}

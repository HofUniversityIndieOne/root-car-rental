<?php


class CarRentalCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Show the home page');
        $I->amOnPage('/');
        $I->see('Welcome!', '#content .container header > h2');

        $navBarSelector = 'body > div.body-bg > header > div > nav';
        $I->see('Home', $navBarSelector);
        $I->see('Products', $navBarSelector);
        $I->see('About us', $navBarSelector);
        $I->see('Login', $navBarSelector);
        $I->click('Products', $navBarSelector);
        $I->see('Rent a Car');
        // save screenshot to tests/_output/debug/
        $I->makeScreenshot('products_page');
    }
}

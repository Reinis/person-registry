<?php

namespace PersonRegistryTest\acceptance;

use AcceptanceTester;
use DateTime;

class TokenCest
{
    public function failToGoToTheDashboardWithoutLogin(AcceptanceTester $I): void
    {
        $I->amOnPage('/');

        $I->amGoingTo("go to the dashboard");
        $I->amOnPage('/dashboard');

        $I->expectTo("be brought to the login page instead");
        $I->seeCurrentUrlEquals('/login');
    }

    public function failToGetATokenForAnEmptyNationalId(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');

        $I->amGoingTo("ask for a token without a national id");
        $I->click('Login');

        $I->expectTo("see error page");
        $I->seeInTitle('Error');
        $I->see('Unknown user');
    }

    public function failToGetATokenForAnInvalidNationalId(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');

        $I->amGoingTo("ask for a token with an invalid national id");
        $I->fillField('nid', 'abcdef-ghijk');
        $I->click('Login');

        $I->expectTo("see error page");
        $I->seeInTitle('Error');
        $I->see('Unknown user');
    }

    public function failToGetATokenForANonexistentNationalId(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');

        $I->amGoingTo("ask for a token for a nonexistent person");
        $I->fillField('nid', '000000-00000');
        $I->click('Login');

        $I->expectTo("see error page");
        $I->seeInTitle('Error');
        $I->see('Unknown user');
    }

    public function failToLoginWithAnExpiredToken(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');
        $I->haveInDatabase('people', ['nationalId' => '123456-12345', 'age' => 99]);
        $I->haveInDatabase(
            'tokens',
            [
                'nid' => '123456-12345',
                'token' => 'abc',
                'expiration_time' => (new DateTime('1 min ago'))->format('Y-m-d H:i:s')
            ]
        );

        $I->amGoingTo("login with an outdated token");
        $I->amOnPage('/otp?token=abc');

        $I->expectTo("fail");
        $I->seeInTitle('Error');
        $I->see('Invalid token');
    }

    public function succeedToLoginWithAValidToken(AcceptanceTester $I): void
    {
        $I->haveInDatabase('people', ['nationalId' => '123456-12345', 'age' => 99]);
        $I->haveInDatabase(
            'tokens',
            [
                'nid' => '123456-12345',
                'token' => 'abc',
                'expiration_time' => (new DateTime('+15 min'))->format('Y-m-d H:i:s')
            ]
        );

        $I->amGoingTo("login with a token");
        $I->amOnPage('/otp?token=abc');

        $I->expectTo("succeed");
        $I->seeLink('Dashboard');
        $I->click('Dashboard');
        $I->see('123456-12345');
    }

    public function loginWithAToken(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->seeNumRecords(0, 'people');
        $I->haveInDatabase(
            'people',
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'nationalId' => '123456-12345',
                'age' => 99,
            ]
        );
        $I->seeNumRecords(1, 'people');

        $I->amGoingTo("generate a login token");
        $I->see('Log In');
        $I->click('Log In');
        $I->seeCurrentUrlEquals('/login');
        $I->fillField('nid', '123456-12345');
        $I->click('Login');

        $I->expectTo("have login link with a token generated");
        $I->seeCurrentUrlEquals('/login');
        $I->seeLink('Authenticate with National Id 123456-12345');

        $I->amGoingTo("authenticate using the token");
        $I->click('Authenticate with National Id');

        $I->expectTo("return to the main page with a dashboard link available");
        $I->seeCurrentUrlEquals('/');
        $I->dontSeeLink('Log In');
        $I->seeLink('Dashboard');

        $I->amGoingTo("go to the dashboard");
        $I->click('Dashboard');

        $I->expectTo("see the dashboard with person data");
        $I->seeCurrentUrlEquals('/dashboard');
        $I->see('John Doe');
        $I->see('123456-12345');
        $I->see('99');

        $I->amGoingTo("log out");
        $sessionId = $I->grabCookie('PHPSESSID');
        $I->click('Logout');

        $I->expectTo("return to the main page with new session");
        $I->seeCurrentUrlEquals('/');
        $I->dontSeeLink('Dashboard');
        $I->seeLink('Log In');
        $I->assertNotEquals($sessionId, $I->grabCookie('PHPSESSID'));
    }
}

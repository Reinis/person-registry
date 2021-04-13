<?php

namespace PersonRegistryTest\acceptance;

use AcceptanceTester;

class AddPersonCest
{
    public function addAndRemoveANewPerson(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->seeNumRecords(0, 'people');

        $I->amGoingTo("add a person to the database");
        $I->click('Add');
        $I->seeCurrentUrlEquals('/add');
        $I->fillField('first_name', 'John');
        $I->fillField('last_name', 'Doe');
        $I->fillField('nid', '123456-12345');
        $I->fillField('age', 99);
        $I->click('Submit');

        $I->expectTo("return to the main page with a new user added");
        $I->seeInDatabase(
            'people',
            [
                'id >' => '0',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'nationalId' => '123456-12345',
                'age' => '99',
                'address' => '',
                'notes' => '',
            ]
        );
        $I->seeCurrentUrlEquals('/');
        $I->seeInTitle('Person Registry');
        $I->see('John');

        $I->amGoingTo("remove a person from the data base");
        $I->click('Delete');

        $I->expectTo("not find John");
        $I->seeNumRecords(0, 'people');
        $I->dontSee('John');
    }
}

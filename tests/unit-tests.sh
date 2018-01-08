#!/bin/sh
vendor/bin/phpunit \
    -c vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml \
    web/typo3conf/ext/car_rental/Tests/Unit/
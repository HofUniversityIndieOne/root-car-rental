#!/bin/sh
typo3DatabaseName="t3_new_site_local_test" typo3DatabaseHost="localhost" \
    typo3DatabaseUsername="developer" typo3DatabasePassword="dev" \
    vendor/bin/phpunit \
        -c vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTests.xml \
        web/typo3conf/ext/car_rental/Tests/Functional/

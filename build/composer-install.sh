#!/bin/sh

# activate all extensions that are defined in require section of composer.json
vendor/bin/typo3cms install:generatepackagestates
# setup (create database tables) for previously activated extensions
vendor/bin/typo3cms extension:setupactive

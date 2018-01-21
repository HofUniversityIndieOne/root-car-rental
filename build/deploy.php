<?php
namespace Deployer;

require 'recipe/typo3.php';

// Project name
set('application', 'stage');

// Project repository
set('repository', 'https://github.com/HofUniversityIndieOne/root-car-rental.git');

// [Optional] Allocate tty for git clone. Default value is false.
// This allow you to enter passphrase for keys or add host to known_hosts.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);

// DocumentRoot / WebRoot for the TYPO3 installation
// (note: changed to lowercase 'web' as defined in composer.json)
set('typo3_webroot', 'web');

// Hosts

host('indie.anyhost.it')
    ->user('developer')
    ->set('deploy_path', '~/sites/{{application}}');
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// read database info from .env files into local_db_* & host_db_*
task('dotenv:load', function () {
    $rootDirectory = dirname(__DIR__);
    $localDotEnv = $rootDirectory . '/.env';
    $hostDotEnv = __DIR__ . '/' . get('hostname') . '.env';
    if (!file_exists($localDotEnv)) {
        throw new \RuntimeException(
            'No local .env file given at ' . $rootDirectory
        );
    }
    if (!file_exists($hostDotEnv)) {
        throw new \RuntimeException(
            'No host .env file given at ' . $hostDotEnv
        );
    }
    set('dotenv_file', $hostDotEnv);
    $loader = new \Symfony\Component\Dotenv\Dotenv();
    $localEnv = $loader->parse(file_get_contents($localDotEnv));
    $hostEnv = $loader->parse(file_get_contents($hostDotEnv));
    foreach (['DBNAME', 'HOST', 'USER', 'PASSWORD'] as $key) {
        $lowerKey = strtolower($key);
        set('local_db_' . $lowerKey, $localEnv['TYPO3_DB_' . $key]);
        set('host_db_' . $lowerKey, $hostEnv['TYPO3_DB_' . $key]);
    }
})->desc('Read .env files');

// deploy local database to host for the initial release
task('database:deploy', function () {
    $releases = get('releases_list');
    if (count($releases) === 0) {
        set('database_file', sha1(uniqid()) . '.sql');
        writeln(
            '<info>No releases found, execute local database deployment</info>'
        );
        writeln(
            'Dumping local database to {{database_file}}'
        );
        // create database dump
        runLocally(
            'mysqldump {{local_db_dbname}} -h {{local_db_host}}'
            . ' -u {{local_db_user}} -p{{local_db_password}}'
            . ' > {{database_file}}'
        );
        // upload database dump to database directory
        run("cd {{deploy_path}} && if [ ! -d database ]; then mkdir database; fi");
        upload('{{database_file}}', '{{deploy_path}}/database/');
        // try to import database dump
        try {
            // @todo Optional: Create database if not existing...
            writeln('Deploying database to host');
            run(
                'cd {{deploy_path}} && '
                . 'mysql {{host_db_dbname}} -h {{host_db_host}}'
                . ' -u {{host_db_user}} -p{{host_db_password}}'
                . ' < database/{{database_file}}'
            );
            // in any case remove database dump again
            run("cd {{deploy_path}} && if [ -e database/{{database_file}} ]; then rm database/{{database_file}}; fi");
        } catch (\Exception $exception) {
            // in any case remove database dump again
            run("cd {{deploy_path}} && if [ -e database/{{database_file}} ]; then rm database/{{database_file}}; fi");
            throw $exception;
        }
    } else {
        writeln(
            '<info>Release available. Skipping local database deployment</info>'
        );
    }
})->desc('Deploy local database');

task('dotenv:deploy', function () {
    upload('{{dotenv_file}}', '{{release_path}}/.env');
})->desc('Deploy host .env file');

task('typo3:finish', function() {
    within('{{release_path}}', function () {
        run('vendor/bin/typo3cms install:generatepackagestates');
        run('vendor/bin/typo3cms extension:setupactive');
        run('vendor/bin/typo3cms cache:flush');
    });
})->desc('Finish TYPO3 environment');

# ...
# deploy:info...
before('deploy:prepare', 'dotenv:load');
# deploy:prepare...
# deploy:lock...
before('deploy:release', 'database:deploy');
# deploy:release...
before('deploy:unlock', 'dotenv:deploy');
    after('dotenv:deploy', 'typo3:finish');
# deploy:unlock
# ...

<?php
/**
 * Standalone PHPUnit bootstrap - no WordPress load.
 *
 * The modules under test in this repo are deliberately WordPress-free (see
 * Job_Listing_Filter), so this bootstrap only needs Composer's autoloader.
 *
 * @package Sagiris\ElementorJobOpenings
 */

define( 'SAGIRIS_EJO_TESTING', true );

require __DIR__ . '/../vendor/autoload.php';

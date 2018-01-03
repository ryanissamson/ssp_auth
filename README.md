# ssp_auth
A Drupal module that integrates Drupal and the simplesamlphp library, using the IDP method.

-- SUMMARY --

The ssp_auth module makes it possible for Drupal users log into a SimpleSAMLphp SAML identity provider configured on the same virtual host as the Drupal site. It provides a tightly integrated login experience, sending unauthenticated users to the Drupal login page to log in. As a result it removes the requirement to produce a theme for SimpleSAMLphp since the end-user never seems any of the SimpleSAMLphp pages.

-- PREREQUISITES --

1) You must have SimpleSAMLphp installed and configured as a working identity provider (IdP).

  For more information on installing and configuring SimpleSAMLphp as an IdP visit: http://www.simplesamlphp.org


-- INSTALLATION --

Assuming the prerequisites have been met, installation of this module is just like any other Drupal module.

1) Download the module
2) Uncompress it
3) Move it to the appropriate modules directory (usually, modules/contrib/)
4) Go to the Drupal module administration page for your site
5) Enable the module
6) Configure the module (see below)


-- CONFIGURATION --

The configuration of the module is fairly straight forward. You will need to know the following information:
1) the path to your local SimpleSAMLphp installation
2) the name of the authentication source that uses the drupalauth:External class, this is in (<simplesamlphp_path>/config/authsources.php)


-- TROUBLESHOOTING --


-- CONTACT --

Current Maintainers
* Steve Moitozo (geekwisdom) http://drupal.org/user/1662

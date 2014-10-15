[![Build Status](https://scrutinizer-ci.com/g/magento-hackathon/Simple-Image-Helper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/magento-hackathon/Simple-Image-Helper/build-status/master)

#Simple Image Helper#

## Overview ##

By default when Magento templates load an image from the template it creates an expensive process. This process requires checking of the disk to see if the cached file exists and is still valid. It also requires logic to process and resize a new image if this is the first request of the asset. As this is a view file we want to remove any business logic and ensure that the template has the assetes available without any overhead on the page load.

## Installation ##

To install this module either use modman or install by downloading all files and installing as per default Magento installation guidelines.

## Todo ##

## Support ##

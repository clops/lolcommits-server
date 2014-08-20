LOL Commits Server
==============

A simple web backend/server for receiving and displaying lol-commits images on a nice HTML page. This project is based around a Silex-Template.

## Main features: ##

  * Accepts POST requests from lolcommits and stores image files on the server
  * Provides a front-end interface to view the images in reverse chronological order (real fun begins when there are several people posting to the same server)
  * Supports lolcommit KEYs for authentication

## Stuff to Come ##

  * Store metadata in a local SQL database for easier retrieval
  * Instagrmam-like frontend with waypoint navigation

## Installation ##

Setup the server:

  1. Get the sources to your server/host:
     ```git clone git@github.com:clops/lolcommits-server.git```
  2. Get composer (if you don't have a global one):
     ```curl -s http://getcomposer.org/installer | php```
  3. Run installation of dependancies:
     ```php composer.phar install```
  4. Configure your web-server to deliver ```web/``` as the host root

Setup lolcommits to POST to the server:

  1. In the repository configured to use lolcommits activate the "uploldz" plugin by entering the following in the terminal
    ```lolcommits --config```
  2. Then enter the name of the plugin to configure
    ```uploldz```
  3. When asked to enable, enter
    ```true```
  4. As the endpoint enter the host URL where you've setup the lolcommits-server
  5. In case you want to use a key, enter one, keep empty for default.

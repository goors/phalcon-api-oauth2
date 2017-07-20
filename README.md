phalcon-api-oauth2
==================


This repo contains php oauth2-server auth with ready to use phalcon rest api that supports
post, put, delete, get, options, patch methods.
In rest api there is only few routes and one controller as example.

This repo is made by combining:

- https://github.com/jeteokeeffe/php-hmac-rest-api
- https://github.com/sumeko/phalcon-oauth2

Reason for this is to have both things in one place (this works for me).

How to use:

1) Get access token with api.domain.com/access?client_id=id&client_secret=secret

2) With access token just access you api method api.domain.com/method?token=token from 1)

3) Also you different formats:

    - xml ->
            api.domain.com/method?token=token&format=xml or
            api.domain.com/method.xml?token=token

    - json is supported by default

4) Update app/config/config.php with your own stuff

TODO: Add oauth2 scope and static html page for allow/deny like Google oauth2 or Facebook oauth.

Enjoy and do not forgot to run sql file vendor/league/oauth-server/sql/mysql.sql for oauth2!
For any questions/suggestions please check my profile page and send me email.

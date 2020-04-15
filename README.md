# SnowTricks comunity site

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/624b25551f9e4b0e80bfc3587fb752e5)](https://app.codacy.com/manual/emicheldev/SnowTricks?utm_source=github.com&utm_medium=referral&utm_content=emicheldev/SnowTricks&utm_campaign=Badge_Grade_Dashboard)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/emicheldev/SnowTricks/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/emicheldev/SnowTricks/?branch=develop)

[![Maintainability](https://api.codeclimate.com/v1/badges/40bf276ce48122ba1375/maintainability)](https://codeclimate.com/github/emicheldev/SnowTricks/maintainability)

## How to install

### Requirements

To install, you need

- composer from [https://getcomposer.org/](https://getcomposer.org/)
- Yarn from [https://yarnpkg.com](https://yarnpkg.com/)
- Files from github (or git clone [https://github.com/emicheldev/SnowTricks.git](https://github.com/emicheldev/SnowTricks.git))
- Php version 7.2
- Mysql 5.7 or mariadb 10.2.7

### Installation

- Navigate to the repository where you copied the files
- Copy the .env file to .env.local
- Configure the .env.local file
- Run ```composer install --no-dev --optimize-autoloader``` to download all the backend dependencies for production environment or ```composer install``` for dev environment
- Run ```yarn install``` to download all the frontend dependencies
- Verify that the var folder is writable by the webserver
- Verify that the public/uploads folder is writable by the webserver

## Running the dev environment

- Make sure you are in dev environment in the .env or the .env.local file.
- Make sure your database is running and configured in the .env or .env.local files
- Make sure you have all the dev dependencies by running composer install
- Create the database with ```php bin/console d:d:c```
- Apply the migrations with ```php bin/console d:m:m```
- Load the demo fixtures with ```php bin/console d:f:l```
- Compile the assets with ```yarn encore dev```
- Start the symfony server with ```php bin/console server:run```
- The default admin user is admin/admin
- Multiple users created, all with the password &quot;user&quot;

## The .env file

```APP_ENV=dev``` Dev or Prod environment

```APP_SECRET=``` The secret used by symfony, used by the csrf protection

You should regenerate the APP\_SECRET with 32 characters

([https://symfony.com/doc/current/reference/configuration/framework.html#secret](https://symfony.com/doc/current/reference/configuration/framework.html#secret))

```DATABASE_URL=``` The database connection

```MAILER_URL=``` Smtp server

```ADMIN_EMAIL="admin@localhost.dev"``` the sender of the emails from the site



Configure the paths to the default images used throughout the site
```
DEFAULT_IMAGE_PATH="/img"
DEFAULT_USER_IMAGE="user-default.png"
DEFAULT_TRICK_IMAGE="trick-default.jpg"
DEFAULT_MENU_LOGO="snowtricks-logo-small-text.png"
DEFAULT_FRONTPAGE_IMAGE="frontpage-banner.jpg"
DEFAULT_FRONTPAGE_TEXT="Une phrase d'accroche"
```

The upload image paths. Be careful, this is hardcoded in the fixtures
```
DEFAULT_UPLOAD_USER_IMAGE_PATH="/uploads/user_images"
DEFAULT_UPLOAD_TRICK_IMAGE_PATH="/uploads/trick_images"
```

Do you wish to activate the caroussel when more that 1 picture is selected for a trick.

> This feature is still in development and not considered stable

```PRIMARY_IMAGE_CAROUSEL=false```

The default meta data for the pages 

```
DEFAULT_PAGE_TITLE="My page title, 55 to 64 characters"
DEFAULT_PAGE_DESCRIPTION="a short description that will show up in the google search"
DEFAULT_PAGE_KEYWORDS="the main keywords, this will be overwritten by the tags for each trick. Seperate with comma"
```
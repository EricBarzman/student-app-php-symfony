# STUDENTS MANAGEMENT APP

This app was created to manage students and classes within a school.
I'm putting the code online for my portfolio.

It's made using:

- The `Symfony` framework,
- `PHP`,
- `Webpack Encore`,
- `Bootstrap`,
- a few javascript libraries (for QR code reading)
- some JS code I wrote myself (to take photos from cameras and recover the data into files).

## Installation

### PHP

You need an 8.2 version of PHP or later.

### Symfony

You need a 7.0 version of Symfony or later.

### Node JS

Version 20.11

### Composer / NPM

Once you've cloned the repo, type in the terminal these commands:

```cli
composer install
npm install
npm run build
```

NB : Webpack Encore will be installed and will create a `build` folder within the `public/` folder.
The build folder is used by *bootstrap*. It contains several JS scripts as well.

### Public folder

Within the `public` folder, create an `uploads` folder, and inside it, another folder named `photos`.
You should get this path: `public/uploads/photos`

The public folder must be accessible by the app. The photos folder is used to copy the photo files.

### .env file

Rename the `.env` file to `.env.prod`

On the `.env.prod`, choose your database settings.

### Create an admin

On your command terminal, type:
`symfony console app:create-user (some_username) (some_6_digits_number)`

This will allow you to connect as this user, with the 6 digits pincode.
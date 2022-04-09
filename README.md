## Notes APP (SPA) Laravel and Vue
Here is the summary of the code challenge:

### Versions
- Laravel Version: 8.*
- Vue: 2.*
- Bootstrap: 5.*

### Additional Packages Installed:
- Vue2-Editor (WYSIWYG Editor)
- Laravel/Ui (Auth Scaffolding)
- Laravel/Sanctum (Authentication)
- League/Flysystem-aws-s3-v3 (Remote Storage Config)

### How To Install
1. Extract the tarball file to your system. (This is a tarball of a fresh git clone of the project)
2. Once extracted, go to the folder that was extracted. It should be named "notes-app".
3. Run "php composer.phar install" or simply "composer install".
4. Run "npm install" to download "node_modules".
5. Before we proceed, make sure that you have the ".env" file set up. Make sure to rename ".env.example" to ".env"
6. You may need to enter your database credentials on the ".env" before proceeding
7. Run "php artisan key:generate" to generate "APP_KEY" that will automatically be entered on your ".env" file.
8. Run "php artisan migrate" to run the migration.
9. Once that is done, you may now package your assets using "npm run watch" or "npm run dev" (for development mode) or "npm run production"
10. This last step would be your choice, you could either server your Laravel project using the artisan command "php artisan serve" or if you have your apache or nginx that you can configure you can do so. Just point your directory to the "public" folder.

### Features
- Login, Logout and Register Feature (Authentication)
- Notes CRUD (Authenticated) 
- Notes Sharing (Authenticated)
- Notes File Attachments (Authenticated) supports images, videos and text files.

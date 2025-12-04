# 🚀 Pemira API Backend Setup Guide (Laravel)

Hey there! Welcome to the setup guide for the Pemira API backend, built with Laravel. Follow these steps to get everything running smoothly on your local server.

## 📝 Requirements

Before you begin, make sure your device has the following:

-   **PHP**: Version **8.1** or higher (recommended for this Laravel version)
-   **Composer**: To manage PHP dependencies
-   **MySQL**: Version **5.7** or higher for the database
-   **Node.js** and **npm**: Needed if the app uses frontend assets or Laravel Mix

## ⚙️ Setup Steps

### 1. Clone the Repository

Clone the backend repository from GitHub and navigate to the project directory:

```bash
git clone https://github.com/jstcode-hub/pemira-api.git
cd pemira-api
```

### 2. Install Dependencies

Install all Laravel dependencies using Composer. If you run into any PHP version issues, try the alternative command below.

#### Main Command

```bash
composer install
```

#### Alternative (If You Have PHP Version Issues)

If there's a PHP version mismatch on your device, add the `--ignore-platform-reqs` flag to skip version checks:

```bash
composer install --ignore-platform-reqs
```

> **Note**: The `--ignore-platform-reqs` flag can be a quick fix, but it's best to use PHP version 8.1 or higher to fully match Laravel's requirements.

### 3. Create the `.env` Configuration File

Copy the `.env.example` file to create a new `.env` file to configure the app:

```bash
cp .env.example .env
```

### 4. Update Your `.env` File

Open the `.env` file you just created, and update it with your configuration:

#### Database Configuration

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

#### Google OAuth Configuration

Add your Google OAuth credentials at the bottom of the `.env` file:

```dotenv
# Google OAuth Configuration (BACKEND ONLY - Keep Secret Secure!)
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:3000/authenticate
```

> **🔒 SECURITY CRITICAL**: 
> - The `GOOGLE_CLIENT_SECRET` must **ONLY** be stored here in the backend `.env` file
> - **NEVER** commit this secret to version control
> - **NEVER** expose it to the frontend or client-side code
> - The frontend only needs the `GOOGLE_CLIENT_ID` (which is safe to be public)

#### Session Configuration

For production, update the session domain:

```dotenv
SESSION_DOMAIN=.pemiraif.com
SANCTUM_STATEFUL_DOMAINS=pemiraif.com
```

### 5. Getting Google OAuth Credentials

To set up Google OAuth authentication for the backend:

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project or select an existing one
3. Navigate to **APIs & Services** > **Credentials**
4. Click **Create Credentials** and choose **OAuth Client ID**
5. If prompted, configure the OAuth consent screen first
6. Set **Application Type** to **Web Application**
7. Under **Authorized JavaScript origins**, add:
    ```
    http://localhost:3000
    https://pemiraif.com
    ```
8. Under **Authorized redirect URIs**, add:
    ```
    http://localhost:3000/authenticate
    https://pemiraif.com/authenticate
    ```
9. Click **Create** and you'll receive:
    - **Client ID** → Add to both frontend and backend `.env` files
    - **Client Secret** → Add **ONLY** to backend `.env` file as `GOOGLE_CLIENT_SECRET`

### 6. Generate an Application Key

Laravel needs an application key for encryption. You can generate one with the command:

```bash
php artisan key:generate
```

### 7. Migrate the Database

Now, migrate the required tables to your MySQL database:

```bash
php artisan migrate
```

### 8. Run the Seeder

Populate the database with initial data using the seeder:

```bash
php artisan db:seed
```

### 9. Link Storage

To make public storage accessible, create a symbolic link with the following command:

```bash
php artisan storage:link
```

### 10. Analyze API Documentation

To generate and analyze API documentation using dedoc/scramble, run the following command:

```bash
php artisan scramble:analyze
```

This will parse your controllers and generate documentation accessible at the specified endpoint during development.

### 11. Start the Laravel Server

After everything is set up, start the Laravel server locally with:

```bash
php artisan serve
```

The app will be running at [http://localhost:8000](http://localhost:8000).

---

## 📚 API Documentation

During development, the API documentation is accessible at:

```bash
/docs/api
```

Visit [http://localhost:8000/docs/api](http://localhost:8000/docs/api) while the server is running to explore the available endpoints and their usage.

---

## 📁 Project Structure

```
pemira-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AuthController.php    # OAuth & authentication logic
│   │   └── Middleware/
│   │       └── Authenticate.php      # Authentication middleware
│   └── Models/
│       ├── User.php                  # User model
│       └── WhiteList.php             # Whitelisted voters
├── config/
│   └── services.php                  # Google OAuth configuration
├── routes/
│   ├── api.php                       # API routes
│   └── web.php                       # Web routes
└── database/
    ├── migrations/                   # Database migrations
    └── seeders/                      # Database seeders
```

---

## 🎯 Troubleshooting

-   **Database connection issues?** Double-check your MySQL configuration in the `.env` file.
-   **Composer or PHP errors?** Make sure your PHP version meets the minimum requirement (8.1 or higher).
-   **OAuth authentication failing?** Verify:
    - `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` are correctly set in `.env`
    - The redirect URI in Google Console matches exactly: `http://localhost:3000/authenticate`
    - The frontend is sending the correct authorization code
-   **CORS errors?** Make sure `APP_CLIENT_URL` in `.env` matches your frontend URL
-   **Session/Cookie issues?** Check that `SESSION_DOMAIN` and `SANCTUM_STATEFUL_DOMAINS` are configured correctly

---

## 🛠️ Available Artisan Commands

### Development
- `php artisan serve` - Start development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migration with seeding
- `php artisan db:seed` - Run database seeders

### Code Quality
- `php artisan scramble:analyze` - Generate API documentation
- `php artisan route:list` - List all registered routes
- `php artisan config:clear` - Clear configuration cache
- `php artisan cache:clear` - Clear application cache

### Production
- `php artisan config:cache` - Cache configuration for performance
- `php artisan route:cache` - Cache routes for performance
- `php artisan optimize` - Optimize the framework for production

---

## 🤝 Contributing

If you've been added as a collaborator, please follow these contribution guidelines to keep everything organized:

1. **Create a New Branch**  
   Each collaborator should create their own branch for development using the format `[role]-[name]`. For example:

    ```bash
    git checkout -b backend-dev-john
    ```

    or

    ```bash
    git checkout -b backend-feature-ana
    ```

2. **Make Your Changes**
   - Write clean, readable code
   - Follow Laravel best practices and PSR standards
   - Add proper comments and documentation
   - Test your changes thoroughly

3. **Push to Your Branch**  
   After making changes, push them to your branch:

    ```bash
    git add .
    git commit -m "Descriptive commit message"
    git push origin backend-dev-john
    ```

4. **Create a Pull Request**  
   Once your changes are ready, create a pull request from your branch to the main branch. All pull requests will be reviewed before merging.

> **Note**: Be sure to follow the branch naming format above, and make sure your pull request is ready for review so it can be merged into the main branch.

---

## 🔒 Security Best Practices

1. **Never commit `.env` files** - Always use `.env.example` as a template
2. **Keep secrets secret** - Never expose `GOOGLE_CLIENT_SECRET` or `APP_KEY`
3. **Use HTTPS in production** - Especially important for OAuth flows
4. **Validate all inputs** - Use Laravel's validation features
5. **Keep dependencies updated** - Regularly run `composer update`
6. **Use proper authentication** - Leverage Laravel Sanctum for API authentication

---

## 📞 Support

If you encounter any issues or have questions:

1. Check the **Troubleshooting** section above
2. Review the **API Documentation** at `/docs/api`
3. Check Laravel's official documentation at [laravel.com/docs](https://laravel.com/docs)
4. Reach out to the development team

---

Good luck with the setup, and happy coding! If you run into any issues, feel free to reach out. Hope it all goes smoothly! 😄

# Refurb - Scrap Marketplace & Refurbishing Platform

A comprehensive web application built with Laravel and Vue.js that connects scrap sellers, artists, and general users in a marketplace for buying and refurbishing scrap items.

## 🚀 Features

### User Roles
- **Scrap Sellers**: Upload and manage scrap products
- **Artists**: Browse products, create custom requests, collaborate on projects
- **General Users**: Purchase products, browse marketplace
- **Admin**: Manage products, view feedback, oversee platform

### Core Functionality
- **Product Management**: Upload, edit, and manage scrap products with images
- **Custom Requests**: Users can request custom refurbishing work from artists
- **Collaborative Projects**: Artists can work together on projects
- **E-commerce Features**: Shopping cart, wishlist, checkout, order management
- **Rating System**: Users can rate each other based on transactions
- **Recommendation Engine**: Personalized product recommendations
- **Portfolio System**: Artists can showcase their completed work
- **Messaging System**: Comments on custom requests and projects

## 🛠️ Tech Stack

### Backend
- **Laravel 9.x** - PHP Framework
- **MySQL** - Database
- **Laravel Sanctum** - API Authentication
- **Laravel Vite** - Asset bundling

### Frontend
- **Vue.js 3** - JavaScript Framework
- **Vue Router** - Client-side routing
- **Bootstrap 5** - CSS Framework
- **Axios** - HTTP Client
- **Vue Toastification** - Notifications

## 📋 Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js 16+ and npm
- MySQL 5.7 or higher
- XAMPP (for local development)

## 🔧 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd FYP
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database Configuration
Update your `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=refurb
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Build Assets
```bash
npm run dev
# or for production
npm run build
```

### 9. Start Development Server
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## 📁 Project Structure

```
FYP/
├── app/
│   ├── Http/Controllers/     # API Controllers
│   ├── Models/              # Eloquent Models
│   └── Providers/           # Service Providers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── js/
│   │   ├── components/      # Vue.js components
│   │   ├── router.js        # Vue Router configuration
│   │   └── app.js          # Main JavaScript file
│   ├── css/                # Stylesheets
│   └── views/              # Blade templates
├── routes/
│   ├── api.php             # API routes
│   └── web.php             # Web routes
└── public/
    ├── images/             # Uploaded images
    └── index.php           # Entry point
```

## 🔐 API Authentication

The application uses Laravel Sanctum for API authentication. All protected routes require a Bearer token:

```javascript
Authorization: Bearer <your-token>
```

## 🎨 Key Components

### Vue.js Components
- **Home.vue** - Landing page
- **BrowseScrap.vue** - Product browsing with recommendations
- **AddProduct.vue** - Product upload form
- **CustomRequest.vue** - Custom request creation
- **ProjectCreate.vue** - Project creation
- **AddCart.vue** - Shopping cart management
- **Checkout.vue** - Order checkout process

### Laravel Controllers
- **AuthController** - User authentication and ratings
- **ProductController** - Product CRUD operations
- **CustomRequestController** - Custom request management
- **ProjectController** - Project collaboration
- **OrderController** - Order processing
- **RecommendationController** - Product recommendations

## 📊 Database Schema

### Key Tables
- `users` - User accounts with roles
- `products` - Scrap products with images
- `custom_requests` - Custom refurbishing requests
- `collaborative_projects` - Artist collaboration projects
- `orders` - Purchase orders
- `carts` - Shopping cart items
- `wishlists` - User wishlists

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure production database
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Run `php artisan view:cache`
7. Build production assets: `npm run build`

### Security Considerations
- All file uploads are validated
- API routes are protected with authentication
- Input validation on all forms
- CSRF protection enabled
- SQL injection prevention through Eloquent ORM

## 🔧 Configuration

### File Uploads
Images are stored in `public/images/` directory. Maximum file size is 20MB.

### Supported Image Formats
- JPEG
- PNG
- JPG
- GIF

### Email Configuration
Configure email settings in `.env` for password reset functionality:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🐛 Known Issues

- File cleanup for deleted products needs implementation
- Email verification system needs completion
- Advanced search filters need enhancement

## 📞 Support

For support and questions, please create an issue in the repository.

## 🔄 Version History

- **v1.0.0** - Initial release with core marketplace functionality
- **v1.1.0** - Added recommendation system and collaborative projects
- **v1.2.0** - Enhanced UI/UX and added portfolio system

---

**Built with ❤️ using Laravel and Vue.js**


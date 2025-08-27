# 🏦 Laravel Banking System

A secure and modern banking system built with Laravel 12, featuring user authentication, transaction management, admin panel, and advanced security features including Diffie-Hellman key exchange and HMAC verification.

## ✨ Features

### 🔐 Security Features
- **Multi-factor Authentication**: Email verification and PIN-based security
- **Diffie-Hellman Key Exchange**: Secure key generation for transaction encryption
- **HMAC Verification**: Message integrity and authenticity verification
- **Encrypted Payloads**: Secure transaction data transmission
- **Session-based Security**: Unique session IDs for each transaction

### 👥 User Management
- **User Registration & Login**: Complete authentication system
- **Profile Management**: Update personal information and security settings
- **PIN Management**: Secure PIN-based transaction authorization
- **Email Verification**: Two-step verification process
- **Role-based Access**: Admin and regular user roles

### 💰 Banking Operations
- **Money Transfers**: Secure peer-to-peer transactions
- **Balance Management**: Real-time balance tracking
- **Transaction History**: Complete audit trail of all transactions
- **Ledger System**: Detailed financial record keeping
- **Admin Deposits/Withdrawals**: Administrative banking operations

### 🛠️ Admin Panel
- **User Management**: View and manage all registered users
- **Transaction Monitoring**: Oversee all banking transactions
- **Deposit/Withdrawal Operations**: Administrative banking functions
- **System Dashboard**: Overview of banking system statistics

## 🚀 Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL (via Laravel migrations)
- **Authentication**: Laravel Breeze
- **Security**: Custom Diffie-Hellman implementation
- **Testing**: Pest PHP

## 📋 Prerequisites

Before running this application, make sure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 16.0
- **MySQL** or **PostgreSQL**
- **XAMPP** (for local development)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Kowsik-Roy/Banking_system.git
cd Banking_system
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit your `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start the Application
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 📱 Usage

### For Regular Users

1. **Registration**: Create a new account with email verification
2. **Set PIN**: Configure a secure PIN for transactions
3. **Email Verification**: Complete the verification process
4. **Make Transfers**: Send money to other users securely
5. **View History**: Check transaction history and balance

### For Administrators

1. **Access Admin Panel**: Navigate to `/admin/dashboard`
2. **User Management**: View and manage all users
3. **Banking Operations**: Perform deposits and withdrawals
4. **System Monitoring**: Oversee transaction activities

## 🔧 Configuration

### Mail Configuration
Configure your email settings in `.env` for verification emails:
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email
MAIL_FROM_NAME="${APP_NAME}"
```

### Security Settings
The application includes several security features that can be configured:
- PIN requirements and validation
- Session timeout settings
- Transaction limits
- Encryption parameters

## 🧪 Testing

Run the test suite using Pest:
```bash
php artisan test
```

## 📁 Project Structure

```
Laravel-Banking-System/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin controllers
│   │   │   ├── Auth/            # Authentication controllers
│   │   │   └── ...              # Other controllers
│   │   ├── Middleware/          # Custom middleware
│   │   └── Requests/            # Form requests
│   ├── Models/                  # Eloquent models
│   ├── Services/                # Business logic services
│   └── Mail/                    # Email notifications
├── database/
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── factories/               # Model factories
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── admin/              # Admin panel views
│   │   ├── auth/               # Authentication views
│   │   └── components/         # Reusable components
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript files
└── routes/                     # Application routes
```

## 🔒 Security Features Explained

### Diffie-Hellman Key Exchange
- Generates secure shared keys for each transaction
- Prevents man-in-the-middle attacks
- Ensures transaction confidentiality

### HMAC Verification
- Verifies message integrity
- Prevents tampering with transaction data
- Ensures transaction authenticity

### Encrypted Payloads
- All sensitive transaction data is encrypted
- Uses session-specific encryption keys
- Protects against data breaches

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Kowsik Roy**
- GitHub: [@Kowsik-Roy](https://github.com/Kowsik-Roy)

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Tailwind CSS for the beautiful UI components
- All contributors and testers

## 📞 Support

If you encounter any issues or have questions, please:

1. Check the [Issues](https://github.com/Kowsik-Roy/Banking_system/issues) page
2. Create a new issue with detailed information
3. Contact the maintainer

---

⭐ **Star this repository if you find it helpful!**

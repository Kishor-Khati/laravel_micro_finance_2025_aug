# Nepali Microfinance System - Laravel 11

A comprehensive microfinance management system built with Laravel 11, designed specifically for Nepali microfinance institutions.

## Features

### 🏦 Complete Microfinance Operations
- **Member Management**: Complete member lifecycle with KYC verification
- **Loan Management**: Application, approval, disbursement, and repayment tracking
- **Savings Management**: Multiple account types with deposit/withdrawal capabilities
- **Transaction Management**: Complete financial transaction tracking
- **Expense Management**: Operational expense tracking and approval workflow

### 👥 Role-Based Access Control
- **Super Admin**: Full system access and management
- **Branch Manager**: Branch-level operations and oversight
- **Field Officer**: Member registration and field operations
- **Accountant**: Financial records and ledger management
- **Member**: Personal account access and transaction history

### 📊 Analytics & Reporting
- Real-time dashboard with key metrics
- Monthly financial reports
- Loan disbursement analytics
- Savings trend analysis
- Overdue installment tracking

## Database Structure

### Core Tables
- `branches` - Branch information and management
- `users` - System users with role-based access
- `members` - Member profiles and KYC details
- `loan_types` - Different loan product configurations
- `loans` - Loan applications and management
- `savings_types` - Savings account product types
- `savings_accounts` - Individual savings accounts
- `transactions` - All financial transactions
- `loan_installments` - EMI schedule and payment tracking
- `expenses` - Operational expense management

## Installation

### Prerequisites
- PHP 8.3+
- Composer
- SQLite/MySQL/PostgreSQL
- Node.js (for frontend assets)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-microfinance
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Or configure MySQL/PostgreSQL in .env file
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=microfinance
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Login Credentials

After running the seeders, you can login with these default accounts:

### Super Admin
- **Email**: admin@microfinance.com
- **Password**: password
- **Access**: Full system access

### Branch Manager (Kathmandu)
- **Email**: manager.ktm@microfinance.com
- **Password**: password
- **Access**: Kathmandu branch management

### Field Officer
- **Email**: field.ktm@microfinance.com
- **Password**: password
- **Access**: Member registration and field operations

### Accountant
- **Email**: accountant.ktm@microfinance.com
- **Password**: password
- **Access**: Financial records and accounting

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /logout` - User logout

### Dashboard
- `GET /dashboard` - Main dashboard with statistics

### Members
- `GET /members` - List all members (with filters)
- `POST /members` - Create new member
- `GET /members/{id}` - View member details
- `PUT /members/{id}` - Update member information
- `DELETE /members/{id}` - Delete member

### Loans
- `GET /loans` - List all loans (with filters)
- `POST /loans` - Create loan application
- `GET /loans/{id}` - View loan details
- `POST /loans/{id}/approve` - Approve loan
- `POST /loans/{id}/disburse` - Disburse loan
- `POST /loans/{id}/reject` - Reject loan

### Savings
- `GET /savings` - List all savings accounts
- `POST /savings` - Create savings account
- `GET /savings/{id}` - View account details
- `POST /savings/{id}/deposit` - Make deposit
- `POST /savings/{id}/withdraw` - Make withdrawal

## Business Logic

### Loan Processing
1. **Application**: Members submit loan applications with required documents
2. **Verification**: Field officers verify member details and collateral
3. **Approval**: Branch managers or authorized personnel approve loans
4. **Disbursement**: Approved loans are disbursed to member accounts
5. **Repayment**: Monthly installments are tracked and collected

### Savings Management
- Multiple savings account types with different interest rates
- Withdrawal limits and minimum balance requirements
- Automatic interest calculation and posting
- Transaction history and statement generation

### Member KYC Process
1. **Registration**: Basic member information collection
2. **Document Upload**: Citizenship, photos, and other required documents
3. **Verification**: Staff verification of documents and details
4. **Approval**: KYC approval enables full account access

## File Structure

```
laravel-microfinance/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── MemberController.php
│   │   ├── LoanController.php
│   │   └── SavingsController.php
│   └── Models/
│       ├── Branch.php
│       ├── Member.php
│       ├── Loan.php
│       ├── LoanType.php
│       ├── SavingsAccount.php
│       ├── SavingsType.php
│       ├── Transaction.php
│       ├── LoanInstallment.php
│       ├── Expense.php
│       └── User.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_branches_table.php
│   │   ├── 2024_01_01_000002_create_members_table.php
│   │   ├── 2024_01_01_000003_create_loan_types_table.php
│   │   ├── 2024_01_01_000004_create_loans_table.php
│   │   ├── 2024_01_01_000005_create_savings_types_table.php
│   │   ├── 2024_01_01_000006_create_savings_accounts_table.php
│   │   ├── 2024_01_01_000007_create_transactions_table.php
│   │   ├── 2024_01_01_000008_create_loan_installments_table.php
│   │   ├── 2024_01_01_000009_create_expenses_table.php
│   │   └── 2024_01_01_000010_add_role_fields_to_users_table.php
│   └── seeders/
│       ├── BranchSeeder.php
│       ├── UserSeeder.php
│       ├── LoanTypeSeeder.php
│       ├── SavingsTypeSeeder.php
│       ├── MemberSeeder.php
│       └── DatabaseSeeder.php
└── routes/
    └── web.php
```

## Security Features

- Role-based access control (RBAC)
- Branch-level data isolation
- Input validation and sanitization
- Secure file upload handling
- Authentication and authorization middleware
- CSRF protection
- Password hashing

## Performance Optimizations

- Database indexing for frequently queried fields
- Eager loading for related models
- Pagination for large datasets
- Caching for frequently accessed data
- Query optimization for reports

## Development Guidelines

### Code Standards
- Follow Laravel coding standards
- Use meaningful variable and method names
- Write comprehensive comments for complex logic
- Implement proper error handling

### Database Design
- Follow naming conventions (snake_case for tables/columns)
- Use appropriate data types and constraints
- Implement proper foreign key relationships
- Add indexes for performance optimization

### Testing
```bash
# Run unit tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MemberTest.php
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests for new functionality
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.

---

**Note**: This system is specifically designed for Nepali microfinance institutions and includes features tailored to local regulations and practices.
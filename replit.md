# YAYE DIA BTP - Real Estate Management System

## Overview

YAYE DIA BTP is a comprehensive real estate management system built for managing prospects, properties, sites, lots, and contracts. The application is now being converted from a React/Express stack to a Laravel PHP framework with PostgreSQL database for enhanced scalability and maintainability.

## System Architecture

### Backend Architecture
- **Framework**: Laravel 10+ with PHP 8.2
- **Authentication**: Laravel Breeze with custom roles and permissions
- **Database**: PostgreSQL with Eloquent ORM
- **API Design**: RESTful API with proper error handling and middleware
- **Session Management**: Laravel sessions with database storage

### Frontend Architecture
- **Framework**: Laravel Blade templates with Alpine.js for interactivity
- **UI Components**: Tailwind CSS with custom components
- **Form Handling**: Laravel Form Requests with validation
- **Asset Management**: Laravel Vite for CSS/JS compilation

### Database Architecture
- **Database**: PostgreSQL 
- **ORM**: Eloquent ORM with model relationships
- **Migrations**: Laravel migrations for schema management
- **Seeders**: Database seeders for initial data

## Key Components

### Authentication System
- **Provider**: Laravel Breeze with multi-role support
- **Session Storage**: Database-backed sessions
- **User Management**: Comprehensive user profile system with roles (administrateur, responsable_commercial, commercial)
- **Authorization**: Route-level authentication middleware and policies

### Data Models
- **Users**: Profile management with roles and authentication data
- **Prospects**: Lead management with status tracking (nouveau, en_relance, interesse, converti, abandonne)
- **Sites**: Property development sites with location and pricing data
- **Lots**: Individual property lots with status, position, and pricing
- **Contracts**: Sales contracts linking prospects to lots
- **Payments**: Payment tracking with different types (adhesion, reservation, mensualite)
- **Activity Logs**: Comprehensive audit trail for all system activities

### UI/UX Components
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Component Library**: Reusable Blade components
- **Interactive Elements**: Alpine.js for dynamic behavior
- **Dashboard**: Real-time statistics and activity feeds
- **Data Visualization**: Charts and metrics for business insights

## Data Flow

### Authentication Flow
1. User accesses protected route
2. Laravel middleware checks authentication status
3. Redirects to login if unauthenticated
4. Session-based authentication with role verification
5. User data managed through Eloquent models

### Business Process Flow
1. **Prospect Management**: Create → Assign → Follow-up → Convert
2. **Site Management**: Create site → Add lots → Configure pricing
3. **Sales Process**: Reserve lot → Create contract → Process payments
4. **Activity Tracking**: All actions logged for audit and reporting

### API Data Flow
- Laravel routes handle authenticated requests
- Middleware validates session and authorization
- Controllers process requests with Eloquent models
- Responses include proper error handling and status codes
- Frontend updates via Livewire or AJAX requests

## External Dependencies

### Core Dependencies
- **Laravel Framework**: ^10.0
- **PostgreSQL**: Database management
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework
- **Laravel Breeze**: Authentication scaffolding

### Development Dependencies
- **Laravel Vite**: Asset bundling and compilation
- **Laravel Pint**: Code style fixer
- **PHPUnit**: Testing framework

## Deployment Strategy

### Development Environment
- **Runtime**: PHP 8.2 with PostgreSQL
- **Development Server**: Laravel Artisan serve
- **Database**: PostgreSQL with environment configuration
- **Environment Variables**: .env file with database and app configuration

### Production Deployment
- **Server**: Apache/Nginx with PHP-FPM
- **Asset Compilation**: npm run build for production assets
- **Database Management**: Laravel migrations and seeders
- **Caching**: Redis/Memcached for session and cache storage

## User Preferences

Preferred communication style: Simple, everyday language.

## Changelog

- June 23, 2025: Migration from React/Express to Laravel framework initiated
- Database schema design adapted for Laravel Eloquent models
- Authentication system redesigned using Laravel Breeze
- Complete Laravel project structure created with:
  - PostgreSQL database with comprehensive migrations
  - Eloquent models for all entities (User, Prospect, Site, Lot, Payment, Contract, etc.)
  - Authentication system with role-based access control
  - Controllers for dashboard, prospects, and sites management
  - Tailwind CSS styling with custom components
  - Database seeders with demo data
  - Complete MVC architecture following Laravel conventions

## Demo Accounts
- Administrateur: admin@yayedia.com / admin123
- Responsable Commercial: manager@yayedia.com / manager123
- Commercial: commercial@yayedia.com / commercial123

## Running the Application
Use `./run_laravel.sh` to start the Laravel development server on port 3000.

## Project Status
✅ Complete Laravel project with full MVC architecture
✅ PostgreSQL database with comprehensive migrations
✅ Authentication system with role-based access control  
✅ Responsive UI with Tailwind CSS and Alpine.js
✅ Complete CRUD operations for prospects and sites
✅ Demo data with realistic business scenarios
✅ Production-ready codebase following Laravel best practices

## Application Features
- **Dashboard**: Real-time statistics and recent activities
- **Prospect Management**: Complete CRM functionality with status tracking
- **Site Management**: Property development projects with lot management
- **Payment Processing**: Track payments with confirmation system
- **Contract Generation**: Automated contract creation with payment schedules
- **Role-Based Access**: Administrator, Manager, and Agent roles
- **Activity Logging**: Comprehensive audit trail
- **Responsive Design**: Mobile-first approach with modern UI
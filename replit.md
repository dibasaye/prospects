# YAYE DIA BTP - Real Estate Management System

## Overview

YAYE DIA BTP is a comprehensive real estate management system built for managing prospects, properties, sites, lots, and contracts. The application follows a full-stack architecture with a React frontend and Express backend, using PostgreSQL with Drizzle ORM for data management and Replit Auth for authentication.

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript
- **Routing**: Wouter for client-side routing
- **State Management**: TanStack Query (React Query) for server state management
- **UI Components**: Shadcn/ui with Radix UI primitives and Tailwind CSS
- **Build Tool**: Vite for development and production builds
- **Form Handling**: React Hook Form with Zod validation

### Backend Architecture
- **Framework**: Express.js with TypeScript
- **Authentication**: Replit Auth with OpenID Connect
- **Session Management**: Express sessions with PostgreSQL storage
- **API Design**: RESTful API with proper error handling and middleware
- **Development**: tsx for TypeScript execution

### Database Architecture
- **Database**: PostgreSQL (configured for Neon serverless)
- **ORM**: Drizzle ORM with TypeScript schema definitions
- **Migrations**: Drizzle Kit for schema migrations
- **Connection**: Connection pooling with @neondatabase/serverless

## Key Components

### Authentication System
- **Provider**: Replit Auth with OIDC discovery
- **Session Storage**: PostgreSQL-backed sessions using connect-pg-simple
- **User Management**: Comprehensive user profile system with roles (administrateur, responsable_commercial, commercial)
- **Authorization**: Route-level authentication middleware

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
- **Component Library**: Comprehensive Shadcn/ui components
- **Interactive Elements**: Advanced form handling, data tables, modals, and toast notifications
- **Dashboard**: Real-time statistics and activity feeds
- **Data Visualization**: Charts and metrics for business insights

## Data Flow

### Authentication Flow
1. User accesses protected route
2. Middleware checks authentication status
3. Redirects to Replit Auth if unauthenticated
4. OIDC flow completes and creates/updates user session
5. User data stored in PostgreSQL with role-based access

### Business Process Flow
1. **Prospect Management**: Create → Assign → Follow-up → Convert
2. **Site Management**: Create site → Add lots → Configure pricing
3. **Sales Process**: Reserve lot → Create contract → Process payments
4. **Activity Tracking**: All actions logged for audit and reporting

### API Data Flow
- Client makes authenticated API requests
- Express middleware validates session and authorization
- Business logic processes requests with database operations
- Responses include proper error handling and status codes
- Frontend updates UI state via React Query cache invalidation

## External Dependencies

### Core Dependencies
- **@neondatabase/serverless**: PostgreSQL connection for serverless environments
- **drizzle-orm**: TypeScript ORM with excellent type safety
- **@tanstack/react-query**: Server state management and caching
- **@radix-ui/***: Accessible UI component primitives
- **openid-client**: OIDC authentication implementation
- **express-session**: Session management middleware

### Development Dependencies
- **TypeScript**: Full type safety across the stack
- **Vite**: Fast development and optimized production builds
- **Tailwind CSS**: Utility-first CSS framework
- **ESBuild**: Fast JavaScript bundling for production

### Replit-Specific Integrations
- **@replit/vite-plugin-runtime-error-modal**: Development error handling
- **@replit/vite-plugin-cartographer**: Development environment integration

## Deployment Strategy

### Development Environment
- **Runtime**: Node.js 20 with PostgreSQL 16
- **Development Server**: Vite dev server with HMR
- **Database**: Automatic PostgreSQL provisioning via Replit
- **Environment Variables**: DATABASE_URL, SESSION_SECRET, REPL_ID, ISSUER_URL, REPLIT_DOMAINS

### Production Deployment
- **Build Process**: Vite builds client assets, ESBuild bundles server
- **Deployment Target**: Autoscale deployment on Replit
- **Port Configuration**: Internal port 5000, external port 80
- **Static Assets**: Served from dist/public directory

### Database Management
- **Schema Migrations**: `npm run db:push` for development
- **Connection Pooling**: Configured for serverless environments
- **Session Storage**: PostgreSQL-backed session management

## User Preferences

Preferred communication style: Simple, everyday language.

## Changelog

Changelog:
- June 23, 2025. Initial setup
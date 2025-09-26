# User Role Structure

## Overview
The Foundation CRM system uses a simplified 3-tier role structure where users can have different levels of access based on their role.

## User Roles

### 1. SUPER_ADMIN
- **Access Level**: Full administrative access
- **Capabilities**:
  - Manage all users (create, edit, delete)
  - Access admin dashboard
  - Manage all donations (view, edit, assign, complete)
  - Access finance management (accounts, expenses, transfers, reports)
  - Manage materialistic donations
  - Manage volunteer assignments
  - Manage localization (countries, states, cities)
  - Full system administration

### 2. VOLUNTEER
- **Access Level**: Limited administrative access
- **Capabilities**:
  - Access volunteer dashboard
  - Manage assigned donations (view, update status, add remarks)
  - View all materialistic donations (read-only)
  - Update donation status and add remarks
  - Complete monetary donations with account selection
  - Handle currency conversion for donations
  - **Make personal donations** (monetary, materialistic, service)
  - View and manage their own personal donations
  - Update status of their own donations

### 3. Normal User (No Specific Role)
- **Access Level**: Basic user access
- **Capabilities**:
  - Make donations (monetary, materialistic, service)
  - View their own donations
  - Update donation status (if they are the donor)
  - Cancel pending/assigned donations
  - Request benefits/assistance
  - Access donation details and history

## Key Changes from Previous Structure

### Before:
- SUPER_ADMIN
- VOLUNTEER
- DONOR (could only donate)
- BENEFICIARY (could only request benefits)

### After:
- SUPER_ADMIN
- VOLUNTEER
- Normal User (can both donate AND request benefits)

## Rationale

The previous structure separated donors and beneficiaries into different roles, but in practice:
- Donors can also become beneficiaries when they need assistance
- Beneficiaries can also donate when they have resources
- This creates unnecessary complexity in role management

The new structure recognizes that users are people who can both give and receive help, making the system more flexible and user-friendly.

## Technical Implementation

### Database Changes:
- Removed `DONOR` and `BENEFICIARY` roles from the roles table
- Existing users with these roles are now normal users (no specific role)
- Updated seeders and migrations accordingly

### Middleware Updates:
- `RoleBasedRedirect` middleware now redirects normal users to their donations page
- Role-based access control still works for admin and volunteer areas

### Route Protection:
- Admin routes: `role:SUPER_ADMIN`
- Volunteer routes: `role:VOLUNTEER`
- Public routes: Available to all authenticated users
- Normal users access donation functionality through public routes

## Demo Users

After running the seeder, you'll have these demo accounts:

1. **Super Admin**
   - Phone: 9000000001
   - Password: Demo@123
   - Access: Full admin dashboard

2. **Volunteer**
   - Phone: 9000000002
   - Password: Demo@123
   - Access: Volunteer dashboard + personal donation functionality

3. **Normal User**
   - Phone: 9000000003
   - Password: Demo@123
   - Access: Donation functionality (can donate and request benefits)

## Benefits of New Structure

1. **Simplified Management**: Only 2 specific roles to manage instead of 4
2. **Flexible Users**: Users can both donate and request assistance
3. **Reduced Complexity**: Less role-based logic throughout the application
4. **Better UX**: Users don't need to choose between being a donor or beneficiary
5. **Easier Onboarding**: New users don't need to understand role distinctions

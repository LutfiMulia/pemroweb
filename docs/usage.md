# Usage Guide

## Overview

Insidentia is a web-based reporting system designed to manage and track reports efficiently. This guide will help you understand how to use all features of the system.

## User Roles

### Administrator
- Full access to all features
- User management
- System configuration
- Report oversight and management
- Analytics and reporting

### Regular User
- Submit reports
- View own reports
- Edit pending reports
- Track report status

## Getting Started

### First Login

1. Navigate to the login page: `http://localhost/insidentia/src/auth/login.php`
2. Use default admin credentials:
   - Email: `admin@insidentia.local`
   - Password: `password`
3. Change the default password immediately after first login

### Dashboard Overview

After login, you'll see the main dashboard with:
- **Statistics Cards**: Quick overview of system metrics
- **Recent Reports**: Latest submitted reports
- **Quick Actions**: Shortcuts to common tasks
- **System Status**: Current system health

## User Management

### Creating New Users

**Admin only feature**

1. Go to **Users** → **Add New User**
2. Fill in required information:
   - Full Name
   - Email Address
   - Password
   - Role (Admin/User)
   - Status (Active/Inactive)
3. Click **Save** to create the user

### Managing Existing Users

1. Navigate to **Users** → **All Users**
2. Use the search bar to find specific users
3. Click on a user to view/edit details
4. Available actions:
   - Edit user information
   - Change password
   - Activate/Deactivate account
   - Delete user (with confirmation)

### User Profile Management

All users can:
1. Click on profile icon in top-right corner
2. Select **Profile Settings**
3. Update:
   - Personal information
   - Email address
   - Password
   - Profile picture (if enabled)

## Report Management

### Submitting a Report

1. Click **New Report** or **Reports** → **Submit Report**
2. Fill in the form:
   - **Title**: Brief description of the issue
   - **Category**: Select appropriate category
   - **Priority**: Low, Medium, or High
   - **Location**: Where the issue occurred
   - **Description**: Detailed explanation
   - **Attachments**: Upload relevant files (optional)
3. Click **Submit Report**

### Report Categories

Default categories include:
- **Infrastruktur**: Infrastructure-related issues
- **Kebersihan**: Cleanliness and sanitation
- **Keamanan**: Security concerns
- **Pelayanan**: Service-related issues

### Report Status

Reports can have the following statuses:
- **Pending**: Newly submitted, awaiting review
- **Processing**: Under investigation/being worked on
- **Completed**: Issue resolved
- **Rejected**: Report declined with reason

### Viewing Reports

#### For Regular Users:
1. Go to **My Reports**
2. View list of all submitted reports
3. Click on any report to see details
4. Filter by status, category, or date

#### For Administrators:
1. Go to **Reports** → **All Reports**
2. View all reports from all users
3. Use filters to narrow down results:
   - Status
   - Category
   - Priority
   - Date range
   - User
4. Bulk actions available for multiple reports

### Editing Reports

**Users can edit their own reports only when status is "Pending"**

1. Go to **My Reports**
2. Click on a pending report
3. Click **Edit** button
4. Make necessary changes
5. Click **Update Report**

### Report Actions (Admin Only)

1. **Change Status**: Update report status
2. **Assign Priority**: Set priority level
3. **Add Comments**: Internal notes
4. **Attach Files**: Additional documentation
5. **Send Notifications**: Alert relevant parties

## Categories Management

### Adding New Categories

**Admin only feature**

1. Go to **Categories** → **Add New**
2. Fill in details:
   - Category Name
   - Description
   - Color (for visual identification)
   - Status (Active/Inactive)
3. Click **Save**

### Managing Categories

1. Navigate to **Categories** → **All Categories**
2. View all existing categories
3. Edit, activate/deactivate, or delete categories
4. Reorder categories by dragging (if enabled)

## Search and Filtering

### Quick Search
- Use the search bar in the top navigation
- Search across reports, users, and categories
- Results are displayed in real-time

### Advanced Filtering

#### Report Filters:
- **Status**: Filter by report status
- **Category**: Filter by category type
- **Priority**: Filter by priority level
- **Date Range**: Custom date selection
- **User**: Filter by reporter (admin only)

#### User Filters:
- **Role**: Filter by user role
- **Status**: Active/Inactive users
- **Registration Date**: Filter by join date

## Notifications

### Email Notifications

The system sends email notifications for:
- New report submissions
- Status changes
- Password reset requests
- User account creation

### In-App Notifications

- Real-time notifications appear in the top bar
- Click on notification icon to view all alerts
- Notifications are marked as read when viewed

## Settings and Configuration

### System Settings (Admin Only)

1. Go to **Settings** → **System Configuration**
2. Configure:
   - Site title and description
   - Email settings
   - Notification preferences
   - File upload limits
   - Theme settings

### User Preferences

1. Click **Profile** → **Settings**
2. Configure:
   - Email notifications
   - Language preference
   - Time zone
   - Theme (light/dark)

## Data Export and Reporting

### Export Reports

1. Go to **Reports** → **Export**
2. Select export format:
   - PDF
   - Excel (CSV)
   - JSON
3. Choose date range and filters
4. Click **Export**

### Generate Analytics

**Admin only feature**

1. Navigate to **Analytics** → **Reports**
2. Select date range
3. Choose metrics to display:
   - Total reports
   - Reports by category
   - Reports by status
   - User activity
4. View charts and graphs
5. Export analytics data

## File Management

### Uploading Files

1. When submitting/editing a report
2. Click **Add Attachment**
3. Select file(s) from your device
4. Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF
5. Maximum file size: 10MB per file

### Managing Attachments

1. View attached files in report details
2. Download files by clicking the download icon
3. Delete attachments (admin only)
4. Preview images directly in browser

## Mobile Usage

### Responsive Design

- Fully responsive interface
- Works on tablets and smartphones
- Touch-friendly navigation
- Optimized forms for mobile input

### Mobile Features

- Quick report submission
- Photo capture for attachments
- Location services integration
- Push notifications (if enabled)

## Security Features

### Password Security

- Minimum password requirements
- Password strength indicator
- Regular password change prompts
- Two-factor authentication (optional)

### Data Protection

- All data is encrypted in transit
- Secure session management
- Input validation and sanitization
- SQL injection prevention

### Access Control

- Role-based permissions
- Session timeout after inactivity
- Login attempt monitoring
- Account lockout after failed attempts

## Troubleshooting

### Common Issues

#### Cannot Login
1. Check email and password
2. Ensure account is active
3. Try password reset
4. Contact administrator

#### Cannot Submit Report
1. Check all required fields
2. Verify file size limits
3. Ensure stable internet connection
4. Try refreshing the page

#### Missing Notifications
1. Check email spam folder
2. Verify notification settings
3. Ensure email is correct
4. Contact administrator

#### Slow Performance
1. Check internet connection
2. Clear browser cache
3. Try different browser
4. Report to administrator

### Getting Help

1. **In-App Help**: Click help icon for context-sensitive help
2. **User Manual**: Available in PDF format
3. **Contact Support**: Use built-in contact form
4. **Community Forum**: Join discussions with other users

## Keyboard Shortcuts

### Global Shortcuts
- `Ctrl + /`: Open search
- `Ctrl + N`: New report (if available)
- `Ctrl + H`: Go to home/dashboard
- `Ctrl + L`: Logout
- `Esc`: Close modals/dialogs

### Report Shortcuts
- `Ctrl + S`: Save report
- `Ctrl + Enter`: Submit report
- `Tab`: Navigate between fields
- `Shift + Tab`: Navigate backwards

## Best Practices

### For Users

1. **Clear Titles**: Use descriptive report titles
2. **Detailed Descriptions**: Provide comprehensive information
3. **Appropriate Categories**: Select correct category
4. **Accurate Priority**: Set realistic priority levels
5. **Follow Up**: Monitor report status regularly

### For Administrators

1. **Regular Monitoring**: Check reports daily
2. **Timely Responses**: Update status promptly
3. **Clear Communication**: Provide detailed feedback
4. **User Training**: Educate users on best practices
5. **System Maintenance**: Regular backups and updates

## Advanced Features

### Bulk Operations

**Admin only**

1. Select multiple reports using checkboxes
2. Choose bulk action:
   - Change status
   - Assign category
   - Delete reports
   - Export selected
3. Confirm action

### Custom Fields

**Admin configuration**

1. Go to **Settings** → **Custom Fields**
2. Add new fields to reports:
   - Text fields
   - Dropdown lists
   - Date fields
   - Number fields
3. Configure field properties
4. Activate for report forms

### API Integration

For developers:
1. Access API documentation at `/api/docs`
2. Generate API keys in settings
3. Use RESTful endpoints for:
   - Report CRUD operations
   - User management
   - Category management
   - File uploads

## Tips and Tricks

### Efficient Reporting
- Use templates for common reports
- Save frequently used filters
- Utilize keyboard shortcuts
- Set up email notifications

### Better Organization
- Create meaningful categories
- Use consistent naming conventions
- Regular cleanup of old reports
- Archive completed reports

### Performance Optimization
- Limit attachment file sizes
- Use appropriate image formats
- Clear browser cache regularly
- Log out when not in use

## Updates and Maintenance

### System Updates
- Regular updates are released
- Check changelog for new features
- Backup data before updates
- Test functionality after updates

### Data Backup
- Regular automated backups
- Download personal data
- Export important reports
- Keep offline copies

## Conclusion

This usage guide covers the main features of Insidentia. For additional help:
- Check the FAQ section
- Contact system administrator
- Refer to technical documentation
- Join community discussions

Remember to keep your login credentials secure and report any issues to the administrator promptly.

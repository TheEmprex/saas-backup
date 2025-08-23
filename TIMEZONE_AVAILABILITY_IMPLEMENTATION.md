# Timezone-Aware Availability System Implementation

## Overview
This implementation provides a comprehensive timezone-aware availability system that allows users to set their work schedules and enables agencies to search and filter users based on their availability across different timezones.

## Features Implemented

### 1. Database Schema
- **User table extensions**: Added `timezone`, `available_for_work`, `hourly_rate`, `preferred_currency` fields
- **UserAvailabilitySchedule model**: Stores weekly availability with timezone conversion capabilities
- **JobPost extensions**: Added timezone and shift information for agency postings

### 2. Models Created/Updated

#### UserAvailabilitySchedule Model
- Handles individual day availability schedules
- Supports break times as JSON field
- Provides timezone conversion methods
- Includes helper methods for common timezones and days of week

#### User Model Updates
- Added relationship to `availabilitySchedule`
- New fields for timezone preferences and work availability

#### JobPost Model Updates
- Added timezone and shift support for agencies

### 3. Controller Implementation

#### UserTimezoneAvailabilityController
- `index()`: Display availability management page
- `update()`: Update timezone, work preferences, and weekly schedule
- `getAvailabilityInTimezone()`: API endpoint for single user timezone conversion
- `getBulkAvailability()`: API endpoint for multiple users
- `searchByAvailability()`: Search users by availability criteria
- `copyDay()`: Copy schedule from one day to another
- `createTemplate()`: Apply templates to multiple days

### 4. Frontend Views

#### Profile Availability Management (`resources/views/profile/availability.blade.php`)
- Comprehensive timezone selection
- Weekly schedule management with visual interface
- Break time support with add/remove functionality
- Quick templates (weekdays, full-time, part-time, weekends)
- Copy schedule between days functionality
- Interactive JavaScript for dynamic form management

#### Agency User Browser (`resources/views/marketplace/timezone-availability.blade.php`)
- Search users by timezone and availability
- Visual representation of user schedules in agency's timezone
- Filtering by day, time range, and timezone
- User cards with ratings, rates, and availability display
- Direct links to profiles and messaging

### 5. Routes Configured
- `/profile/availability` - User availability management
- `/marketplace/timezone-availability` - Agency user browser
- API endpoints for timezone conversion and user search
- Template and copy functionality routes

## Key Features

### Timezone Conversion
- Automatic conversion of user availability to viewer's timezone
- Support for major world timezones
- Handles daylight saving time transitions
- Real-time timezone detection in browser

### Availability Management
- Weekly schedule with different times per day
- Multiple break periods per day
- Notes field for additional scheduling information
- Quick templates for common work patterns
- Copy schedule between days

### Agency Tools
- Search users by specific availability criteria
- Filter by day of week and time ranges
- View all results in agency's preferred timezone
- Visual calendar-style availability display
- Direct integration with messaging and profiles

### User Experience
- Intuitive drag-and-drop-style interface
- Real-time validation and feedback
- Mobile-responsive design
- Progressive enhancement with JavaScript

## Technical Implementation Details

### Timezone Handling
- Uses PHP Carbon library for timezone calculations
- JavaScript Intl API for browser timezone detection
- Stores times in user's local timezone
- Converts on-demand for display

### Database Design
- Efficient queries with proper indexing considerations
- JSON fields for flexible break time storage
- Relationship-based queries for availability filtering

### API Design
- RESTful endpoints for availability data
- Bulk operations for performance
- Consistent JSON response format
- Proper error handling and validation

## Usage Examples

### Setting User Availability
1. User navigates to `/profile/availability`
2. Selects timezone and work preferences
3. Sets weekly schedule with break times
4. Uses templates for quick setup
5. Copies schedules between days as needed

### Agency Searching Users
1. Agency visits `/marketplace/timezone-availability`
2. Selects viewing timezone and search criteria
3. Filters by day and time range
4. Views results with availability converted to their timezone
5. Contacts users directly from search results

### API Integration
```javascript
// Get user availability in specific timezone
fetch('/api/users/123/availability?timezone=America/New_York')

// Search available users
fetch('/api/users/search-availability?timezone=UTC&day=monday&start_time=09:00&end_time=17:00')

// Get bulk availability for multiple users
fetch('/api/availability/bulk-timezone?timezone=Europe/London&user_ids[]=1&user_ids[]=2')
```

## Migration Commands Applied
```bash
php artisan migrate  # Applied the user_availability_schedules table migration
```

## Files Created/Modified

### Models
- `app/Models/UserAvailabilitySchedule.php` (new)
- `app/Models/User.php` (updated relationships)
- `app/Models/JobPost.php` (updated with timezone fields)

### Controllers
- `app/Http/Controllers/UserTimezoneAvailabilityController.php` (new)

### Views
- `resources/views/profile/availability.blade.php` (new)
- `resources/views/marketplace/timezone-availability.blade.php` (new)

### Database
- `database/migrations/xxxx_add_timezone_availability_to_users.php` (applied)
- `database/migrations/xxxx_create_user_availability_schedules_table.php` (applied)
- `database/migrations/xxxx_add_timezone_to_job_posts.php` (applied)

### Routes
- `routes/web.php` (updated with new routes)

## Next Steps / Enhancements

1. **Notifications**: Add timezone-aware notification scheduling
2. **Calendar Integration**: Export/import calendar formats (iCal, Google Calendar)
3. **Recurring Patterns**: Support for recurring availability patterns
4. **Blackout Dates**: Support for one-off unavailable dates
5. **Team Scheduling**: Coordinate availability across team members
6. **Performance**: Add caching for frequently accessed availability data
7. **Analytics**: Track availability utilization and optimization suggestions

## Security Considerations
- All routes protected with authentication middleware
- Input validation on all timezone and time inputs
- Rate limiting on search API endpoints
- Proper authorization checks for user data access

This implementation provides a solid foundation for timezone-aware scheduling and can be extended with additional features as needed.

# User Model Documentation

The `User` model represents the users of the application. It includes attributes, relationships, and behaviors that define a user.

## Namespace
`App\Models`

## Traits Used
- `HasFactory` - Provides factory support for the model.
- `Notifiable` - Enables notifications for the user.
- `HasApiTokens` - Adds API token support for authentication.
- `HasScore` - Enables score management for the user
  - **Attributes**:
    - `score` (integer): Represents the average of all scores given to the user.
    - `score_count` (integer): Represents the number of scores given by the user.
    - `last_score` (Score) - Represents the last score given to the user.
- `HasRoles` - Adds role and permission management.
  - **Attributes**:
    - `roles` (array): Stores the roles assigned to the user.
    - `permissions` (array): Stores the permissions assigned to the user.
- `HasUserFilamentConfig` - Configures Filament panel access for the user.
- `HasStates` - Enables state management for the user.
  - **Attributes**:
    - `state` (string): Represents the current state of the user.
- `HasUserData` - Manages user data.
  - **Attributes**:
    - `user_data` (UserData): Represents the additional data associated with the user.
- `HasTherapist` - Adds therapist management for the user.
  - **Attributes**:
    - `therapist` (Therapist): Represents the therapist associated with the user.
+    - `is_therapist` (bool): Indicates if the user has a therapist role.	

## Implements
- `Filament\Models\Contracts\FilamentUser`
- `Filament\Models\Contracts\HasName`

## Fillable Attributes
The following attributes can be mass-assigned:
- `name` (string): The user's first name.
- `last_name` (string): The user's last name.
- `email` (string): The user's email address.
- `password` (string): The user's hashed password.
- `state` (string): The user's current state (e.g., active, banned).
- `banned_to` (timestamp): The date until the user is banned.

## Hidden Attributes
The following attributes are hidden from serialization:
- `password`
- `remember_token`

## Casts
The following attributes are cast to specific types:
- `email_verified_at` (datetime)
- `password` (hashed)
- `state` (`AbstractUserState`)
- `banned_to` (datetime)

## Relationships

### `user_data`
- **Type**: `HasOne`
- **Description**: Defines a one-to-one relationship with the `UserData` model.


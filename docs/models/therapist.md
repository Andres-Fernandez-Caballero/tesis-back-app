# Therapist Model Documentation

The `Therapist` model represents therapists in the application. It includes attributes, relationships, and behaviors related to therapists.

## Namespace
`App\Models\Therapists`

## Traits Used
- `HasFactory` - Provides factory support for the model.

## Fillable Attributes
The following attributes can be mass-assigned:
- `type` (string): The type of therapist (e.g., psychologist, physiotherapist).
- `user_id` (foreign key): The ID of the associated user.
- `certificate_file` (string): The path to the therapist's certificate file.
- `certificate_file_name` (string): The name of the therapist's certificate file.
- `certificate_file_create_date` (date, nullable): The creation date of the certificate file.
- `certificate_file_expiration_date` (date, nullable): The expiration date of the certificate file.
- `field_o` (string, nullable): An optional field for additional information.

## Hidden Attributes
The following attributes are hidden from serialization:
- `field_m`
- `field_o`

## Casts
The following attributes are cast to specific types:
- `certificate_file_create_date` (date)
- `certificate_file_expiration_date` (date)

## Relationships

### `user`
- **Type**: `BelongsTo`
- **Description**: Defines a many-to-one relationship with the `User` model.

```php
    Therapist::find(1)->user: User
```

### `announcements`
- **Type**: `HasMany`
- **Description**: Defines a one-to-many relationship with the `Announcement` model.

```php
    Therapist::find(1)->announcements: Collection<Announcement>
```
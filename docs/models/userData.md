# UserData Model Documentation

The `UserData` model represents additional information associated with a user. It includes attributes and relationships that extend the user's profile.

## Namespace
`App\Models`

## Traits Used
- `HasFactory` - Provides factory support for the model.

## Fillable Attributes
The following attributes can be mass-assigned:
- `user_id` (integer): The ID of the associated user.
- `dni` (string): The user's dni number.
- `gender` (string): The user's gender can be [male, female, other] 
- `phone` (string): The user's phone number.
- `address` (string): The user's address.
- `birth_date` (date): The user's date of birth.
- `profile_picture` (string): The path to the user's profile picture.

## Hidden Attributes
The following attributes are hidden from serialization:
- None.

## Casts
The following attributes are cast to specific types:
- `birth_date` (date)

## Relationships

### `user`
- **Type**: `BelongsTo`
- **Description**: Defines a many-to-one relationship with the `User` model.

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
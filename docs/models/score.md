# Score Model Documentation

The `Score` model represent the calification given by another user.
It includes attributes, relationships, and behaviors related to scores.

## Namespace
`App\Models\Users\Scores`

## Traits Used
- `HasFactory` - Provides factory support for the model.

## Fillable Attributes
The `Score` model uses guarded attributes, meaning all attributes are mass-assignable except those explicitly guarded. By default, the `$guarded` property is set to an empty array.

## Attributes
- `id` - The unique identifier for the score.
- `user_id` - The ID of the user who created the score.
- `starts` - A number representing the score given by the user. between 1 and 5.
- `comment` - A comment associated with the score. can be null.

## Relationships

### `user`
- **Type**: `BelongsTo`
- **Description**: Defines a many-to-one relationship with the `User` model.

```php
    Score::find(1)->user: User
```
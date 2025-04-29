# Announcement Model Documentation

The `Announcement` model represents the announcements created by therapists. It includes attributes, relationships, and behaviors related to announcements.

## Namespace
`App\Models\Therapists`

## Traits Used
- `HasFactory` - Provides factory support for the model.
- `HasTags` - Adds tagging functionality to the model.

## Fillable Attributes
The `Announcement` model uses guarded attributes, meaning all attributes are mass-assignable except those explicitly guarded. By default, the `$guarded` property is set to an empty array.

## Relationships

### `therapist`
- **Type**: `BelongsTo`
- **Description**: Defines a many-to-one relationship with the `Therapist` model.

```php
public function therapist(): BelongsTo
{
    return $this->belongsTo(Therapist::class);
}
```

## Accessors

### `getDiciplinesAttribute`
- **Type**: `Accessor`
- **Description**: Retrieves the disciplines associated with the announcement.

```php
    Announcement::find(1)->diciplines;
```

## Mutators

### `setDiciplinesAttribute`
- **Type**: `Mutator`
- **Description**: Sets the disciplines associated with the announcement.

```php
    Announcement::find(1)->diciplines = 'New Discipline';

    Announcement::find(1)->diciplines = [ 'Dicipline1', 'Dicipline2' ] : null;
```
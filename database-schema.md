# The Blog - MySQL Database Schema

## Overview
This document outlines the complete MySQL database structure for The Blog application. The schema supports a modern blog platform with posts, categories, tags, comments, users, media management, and additional features like bookmarks.

---

## Table of Contents
1. [Users](#users)
2. [Posts](#posts)
3. [Categories](#categories)
4. [Tags](#tags)
5. [Post Tags (Junction Table)](#post-tags-junction-table)
6. [Comments](#comments)
7. [Media](#media)
8. [Post Authors (Junction Table)](#post-authors-junction-table)
9. [Category Curators (Junction Table)](#category-curators-junction-table)
10. [Bookmarks](#bookmarks)
11. [Indexes](#indexes)
12. [Relationships Diagram](#relationships-diagram)

---

## Users

Stores user accounts including administrators, authors, and co-authors.

```sql
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `display_name` VARCHAR(200) NULL,
  `avatar_url` VARCHAR(500) NULL,
  `bio` TEXT NULL,
  `role` ENUM('admin', 'author', 'editor', 'curator') NOT NULL DEFAULT 'author',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `email_verified_at` TIMESTAMP NULL,
  `last_login_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `username`: Unique username for login
- `email`: Unique email address
- `password_hash`: Hashed password (bcrypt/argon2)
- `first_name`, `last_name`: User's real name
- `display_name`: Public display name (defaults to first + last if NULL)
- `avatar_url`: URL to user's avatar image
- `bio`: User biography
- `role`: User role (admin, author, editor, curator)
- `status`: Account status
- `email_verified_at`: Email verification timestamp
- `last_login_at`: Last login timestamp
- `created_at`, `updated_at`: Timestamps

---

## Posts

Main blog posts table storing all post content and metadata.

```sql
CREATE TABLE `posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `slug` VARCHAR(500) NOT NULL UNIQUE,
  `excerpt` TEXT NULL,
  `body` LONGTEXT NOT NULL,
  `status` ENUM('draft', 'in-review', 'scheduled', 'published', 'archived') NOT NULL DEFAULT 'draft',
  `primary_category_id` INT UNSIGNED NULL,
  `hero_media_id` INT UNSIGNED NULL,
  `social_image_id` INT UNSIGNED NULL,
  `author_id` INT UNSIGNED NOT NULL,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` VARCHAR(500) NULL,
  `publish_date` DATETIME NULL,
  `timezone` VARCHAR(50) NOT NULL DEFAULT 'UTC',
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `comments_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `reading_time_minutes` INT UNSIGNED NULL,
  `featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` TIMESTAMP NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_primary_category` (`primary_category_id`),
  INDEX `idx_author` (`author_id`),
  INDEX `idx_publish_date` (`publish_date`),
  INDEX `idx_featured` (`featured`),
  INDEX `idx_created_at` (`created_at`),
  FULLTEXT `ft_title_excerpt` (`title`, `excerpt`),
  CONSTRAINT `fk_posts_primary_category` FOREIGN KEY (`primary_category_id`) 
    REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) 
    REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_hero_media` FOREIGN KEY (`hero_media_id`) 
    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_social_image` FOREIGN KEY (`social_image_id`) 
    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `title`: Post title
- `slug`: URL-friendly slug (unique)
- `excerpt`: Short summary/excerpt
- `body`: Full post content (HTML/Markdown)
- `status`: Post status (draft, in-review, scheduled, published, archived)
- `primary_category_id`: Primary category (foreign key)
- `hero_media_id`: Hero image/video (foreign key to media)
- `social_image_id`: Social sharing image (foreign key to media)
- `author_id`: Primary author (foreign key to users)
- `meta_title`: SEO meta title
- `meta_description`: SEO meta description
- `publish_date`: Scheduled/actual publish date and time
- `timezone`: Timezone for publish_date
- `views_count`: View counter
- `comments_count`: Comment counter (denormalized for performance)
- `reading_time_minutes`: Estimated reading time
- `featured`: Whether post is featured
- `created_at`, `updated_at`, `published_at`: Timestamps

---

## Categories

Blog post categories with metadata and styling.

```sql
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL UNIQUE,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `summary` VARCHAR(500) NULL,
  `description` TEXT NULL,
  `hero_media_id` INT UNSIGNED NULL,
  `icon` VARCHAR(100) NULL,
  `palette` VARCHAR(50) NULL,
  `color_theme` VARCHAR(50) NULL,
  `status` ENUM('active', 'featured', 'growing', 'steady', 'priority', 'emerging', 'archived') NOT NULL DEFAULT 'active',
  `display_order` INT NOT NULL DEFAULT 0,
  `posts_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_display_order` (`display_order`),
  FULLTEXT `ft_name_summary` (`name`, `summary`),
  CONSTRAINT `fk_categories_hero_media` FOREIGN KEY (`hero_media_id`) 
    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `name`: Category name (unique)
- `slug`: URL-friendly slug (unique)
- `summary`: Short summary
- `description`: Full description
- `hero_media_id`: Category hero image/video
- `icon`: Icon identifier (e.g., "ion-flash-outline")
- `palette`: Color palette identifier
- `color_theme`: Color theme identifier
- `status`: Category status
- `display_order`: Order for display
- `posts_count`: Post count (denormalized)
- `created_at`, `updated_at`: Timestamps

---

## Tags

Tags for categorizing posts with flexible tagging.

```sql
CREATE TABLE `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `usage_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_usage_count` (`usage_count`),
  FULLTEXT `ft_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `name`: Tag name (unique)
- `slug`: URL-friendly slug (unique)
- `description`: Optional tag description
- `usage_count`: Number of posts using this tag (denormalized)
- `created_at`, `updated_at`: Timestamps

---

## Post Tags (Junction Table)

Many-to-many relationship between posts and tags.

```sql
CREATE TABLE `post_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_post_tag` (`post_id`, `tag_id`),
  INDEX `idx_post` (`post_id`),
  INDEX `idx_tag` (`tag_id`),
  CONSTRAINT `fk_post_tags_post` FOREIGN KEY (`post_id`) 
    REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_post_tags_tag` FOREIGN KEY (`tag_id`) 
    REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `post_id`: Foreign key to posts
- `tag_id`: Foreign key to tags
- `created_at`: Timestamp
- Unique constraint on (`post_id`, `tag_id`) to prevent duplicates

---

## Comments

User comments on blog posts.

```sql
CREATE TABLE `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NULL,
  `author_name` VARCHAR(200) NOT NULL,
  `author_email` VARCHAR(255) NOT NULL,
  `author_url` VARCHAR(500) NULL,
  `author_ip` VARCHAR(45) NULL,
  `body` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'spam', 'trash') NOT NULL DEFAULT 'pending',
  `user_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_post` (`post_id`),
  INDEX `idx_parent` (`parent_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) 
    REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) 
    REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `post_id`: Foreign key to posts
- `parent_id`: For nested/reply comments (self-referencing)
- `author_name`: Commenter's name
- `author_email`: Commenter's email
- `author_url`: Commenter's website (optional)
- `author_ip`: IP address for moderation
- `body`: Comment content
- `status`: Comment moderation status
- `user_id`: If commenter is a registered user (optional)
- `created_at`, `updated_at`: Timestamps

---

## Media

Media files (images, videos) used throughout the application.

```sql
CREATE TABLE `media` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_filename` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_url` VARCHAR(500) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` BIGINT UNSIGNED NOT NULL,
  `width` INT UNSIGNED NULL,
  `height` INT UNSIGNED NULL,
  `duration` INT UNSIGNED NULL,
  `alt_text` VARCHAR(500) NULL,
  `caption` TEXT NULL,
  `uploaded_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_uploaded_by` (`uploaded_by`),
  INDEX `idx_mime_type` (`mime_type`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_media_uploaded_by` FOREIGN KEY (`uploaded_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `filename`: Stored filename
- `original_filename`: Original upload filename
- `file_path`: Server file path
- `file_url`: Public URL
- `mime_type`: MIME type (image/jpeg, video/mp4, etc.)
- `file_size`: File size in bytes
- `width`: Image/video width (pixels)
- `height`: Image/video height (pixels)
- `duration`: Video duration in seconds (if video)
- `alt_text`: Alt text for accessibility
- `caption`: Media caption
- `uploaded_by`: User who uploaded (foreign key)
- `created_at`, `updated_at`: Timestamps

---

## Post Authors (Junction Table)

Many-to-many relationship for co-authors on posts.

```sql
CREATE TABLE `post_authors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `is_primary` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_post_author` (`post_id`, `user_id`),
  INDEX `idx_post` (`post_id`),
  INDEX `idx_user` (`user_id`),
  CONSTRAINT `fk_post_authors_post` FOREIGN KEY (`post_id`) 
    REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_post_authors_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `post_id`: Foreign key to posts
- `user_id`: Foreign key to users
- `is_primary`: Whether this is the primary author
- `created_at`: Timestamp
- Unique constraint on (`post_id`, `user_id`)

---

## Category Curators (Junction Table)

Many-to-many relationship for category curators.

```sql
CREATE TABLE `category_curators` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `is_lead` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_category_curator` (`category_id`, `user_id`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_user` (`user_id`),
  CONSTRAINT `fk_category_curators_category` FOREIGN KEY (`category_id`) 
    REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_category_curators_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `category_id`: Foreign key to categories
- `user_id`: Foreign key to users
- `is_lead`: Whether this is the lead curator
- `created_at`: Timestamp
- Unique constraint on (`category_id`, `user_id`)

---

## Bookmarks

User bookmarks for posts (mentioned in navigation).

```sql
CREATE TABLE `bookmarks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `post_id` INT UNSIGNED NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_bookmark` (`user_id`, `post_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_post` (`post_id`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookmarks_post` FOREIGN KEY (`post_id`) 
    REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Fields:**
- `id`: Primary key
- `user_id`: Foreign key to users
- `post_id`: Foreign key to posts
- `notes`: Optional user notes
- `created_at`: Timestamp
- Unique constraint on (`user_id`, `post_id`)


## Indexes

### Performance Indexes
All tables include appropriate indexes for:
- Foreign keys
- Frequently queried columns (status, slug, dates)
- Full-text search (title, excerpt, name fields)
- Unique constraints

### Recommended Additional Indexes
```sql
-- Composite indexes for common queries
CREATE INDEX `idx_posts_status_publish_date` ON `posts` (`status`, `publish_date`);
CREATE INDEX `idx_posts_category_status` ON `posts` (`primary_category_id`, `status`);
CREATE INDEX `idx_comments_post_status` ON `comments` (`post_id`, `status`);
```

---

## Relationships Diagram

```
users
  ├── posts (author_id) [1:N]
  ├── post_authors (user_id) [N:M via junction]
  ├── category_curators (user_id) [N:M via junction]
  ├── comments (user_id) [1:N, optional]
  ├── media (uploaded_by) [1:N]
  ├── bookmarks (user_id) [1:N]

posts
  ├── categories (primary_category_id) [N:1]
  ├── users (author_id) [N:1]
  ├── media (hero_media_id, social_image_id) [N:1]
  ├── post_tags (post_id) [N:M via junction]
  ├── post_authors (post_id) [N:M via junction]
  ├── comments (post_id) [1:N]
  └── bookmarks (post_id) [1:N]

categories
  ├── posts (primary_category_id) [1:N]
  ├── media (hero_media_id) [N:1]
  └── category_curators (category_id) [N:M via junction]

tags
  └── post_tags (tag_id) [N:M via junction]

media
  ├── posts (hero_media_id, social_image_id) [1:N]
  ├── categories (hero_media_id) [1:N]
  └── users (uploaded_by) [N:1]

comments
  ├── posts (post_id) [N:1]
  ├── comments (parent_id) [self-referencing for replies]
  └── users (user_id) [N:1, optional]
```

---

## Notes

### Character Set
All tables use `utf8mb4` character set and `utf8mb4_unicode_ci` collation to support full Unicode including emojis.

### Timestamps
- All tables include `created_at` and `updated_at` timestamps
- `updated_at` automatically updates on row modification
- `published_at` in posts is set when status changes to 'published'

### Denormalization
Some counters are denormalized for performance:
- `posts.comments_count` - Updated via triggers or application logic
- `posts.views_count` - Updated on view
- `categories.posts_count` - Updated when posts are added/removed
- `tags.usage_count` - Updated when tags are added/removed

### Soft Deletes
Consider adding `deleted_at` timestamp columns if soft delete functionality is needed.

### Foreign Key Constraints
- `ON DELETE CASCADE`: Junction tables and comments (cascade deletes)
- `ON DELETE SET NULL`: Optional relationships (media, categories)
- `ON DELETE RESTRICT`: Critical relationships (post author)

---

## Sample Queries

### Get published posts with category and author
```sql
SELECT 
  p.id, p.title, p.slug, p.excerpt, p.publish_date,
  c.name AS category_name, c.slug AS category_slug,
  u.display_name AS author_name
FROM posts p
LEFT JOIN categories c ON p.primary_category_id = c.id
LEFT JOIN users u ON p.author_id = u.id
WHERE p.status = 'published'
  AND p.publish_date <= NOW()
ORDER BY p.publish_date DESC;
```

### Get post with all tags
```sql
SELECT 
  p.id, p.title,
  GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tags
FROM posts p
LEFT JOIN post_tags pt ON p.id = pt.post_id
LEFT JOIN tags t ON pt.tag_id = t.id
WHERE p.id = ?
GROUP BY p.id;
```

### Get comments for a post (with nested replies)
```sql
SELECT 
  c.id, c.body, c.author_name, c.created_at,
  c.parent_id, u.display_name AS user_display_name
FROM comments c
LEFT JOIN users u ON c.user_id = u.id
WHERE c.post_id = ? AND c.status = 'approved'
ORDER BY c.created_at ASC;
```

---

## Version History
- **v1.0** - Initial schema design based on application requirements


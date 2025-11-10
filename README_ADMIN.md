# Create Admin User Script

This Python script allows you to create an admin user for The Blog application.

## Installation

1. Install Python dependencies:
```bash
pip install -r requirements.txt
```

Or install manually:
```bash
pip install mysql-connector-python bcrypt
```

## Usage

### Interactive Mode (Recommended)

Run the script without arguments to be prompted for all information:

```bash
python create_admin.py
```

### Command-Line Mode

Provide all required information as arguments:

```bash
python create_admin.py --username admin --email admin@example.com \
                       --password secret123 --first-name Admin --last-name User
```

### Options

- `-u, --username`: Username (required)
- `-e, --email`: Email address (required)
- `-p, --password`: Password (required, or use `-P` for secure prompt)
- `-P, --password-prompt`: Prompt for password securely
- `-f, --first-name`: First name (required)
- `-l, --last-name`: Last name (required)
- `-d, --display-name`: Display name (optional)
- `-b, --bio`: Bio (optional)

### Examples

**Interactive mode:**
```bash
python create_admin.py
```

**Command-line with password:**
```bash
python create_admin.py -u admin -e admin@example.com -p secret123 \
                       -f Admin -l User
```

**Command-line with secure password prompt:**
```bash
python create_admin.py -u admin -e admin@example.com -P \
                       -f Admin -l User
```

**With display name and bio:**
```bash
python create_admin.py -u admin -e admin@example.com -p secret123 \
                       -f Admin -l User -d "Admin User" -b "Site administrator"
```

## Database Configuration

The script uses the database configuration from `config.php`:
- Host: localhost
- Port: 3307
- User: root
- Password: (empty)
- Database: theblog

To change these settings, edit the `DB_CONFIG` dictionary in `create_admin.py`.

## Notes

- The script checks if a user with the same username or email already exists
- Passwords are hashed using bcrypt (compatible with PHP's `password_hash()`)
- The created user will have:
  - Role: `admin`
  - Status: `active`
  - Email verified timestamp set to current time

## Troubleshooting

**Error: mysql-connector-python is required**
- Install it: `pip install mysql-connector-python`

**Error: bcrypt is required**
- Install it: `pip install bcrypt`

**Error: Database connection failed**
- Check that MySQL is running
- Verify database credentials in `create_admin.py`
- Ensure the database `theblog` exists

**Error: User already exists**
- The username or email is already in use
- Choose a different username or email


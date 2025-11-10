-- Sample Tech Posts
-- Replace author_id (1) with your actual user ID
-- Category ID 1 is assumed to be "Tech"

-- Post 1: Getting Started with React Hooks
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'Getting Started with React Hooks: A Complete Guide',
    'getting-started-with-react-hooks-complete-guide',
    'React Hooks revolutionized how we write React components. Learn the fundamentals of useState, useEffect, and custom hooks in this comprehensive guide.',
    'React Hooks were introduced in React 16.8 and have fundamentally changed how we write React components. They allow us to use state and other React features without writing a class component.

## What are React Hooks?

Hooks are functions that let you "hook into" React state and lifecycle features from function components. Before hooks, if you wanted to use state or lifecycle methods, you had to convert your function component to a class component.

## The useState Hook

The useState hook is the most commonly used hook. It allows you to add state to functional components.

```javascript
import { useState } from ''react'';

function Counter() {
  const [count, setCount] = useState(0);

  return (
    <div>
      <p>You clicked {count} times</p>
      <button onClick={() => setCount(count + 1)}>
        Click me
      </button>
    </div>
  );
}
```

## The useEffect Hook

The useEffect hook lets you perform side effects in function components. It serves the same purpose as componentDidMount, componentDidUpdate, and componentWillUnmount combined in React classes.

```javascript
import { useState, useEffect } from ''react'';

function Example() {
  const [count, setCount] = useState(0);

  useEffect(() => {
    document.title = `You clicked ${count} times`;
  });

  return (
    <div>
      <p>You clicked {count} times</p>
      <button onClick={() => setCount(count + 1)}>
        Click me
      </button>
    </div>
  );
}
```

## Custom Hooks

You can create your own hooks to extract component logic into reusable functions. Custom hooks are just JavaScript functions whose names start with "use".

```javascript
function useCounter(initialValue = 0) {
  const [count, setCount] = useState(initialValue);

  const increment = () => setCount(count + 1);
  const decrement = () => setCount(count - 1);
  const reset = () => setCount(initialValue);

  return { count, increment, decrement, reset };
}
```

## Best Practices

1. Only call hooks at the top level - don''t call hooks inside loops, conditions, or nested functions
2. Only call hooks from React function components or custom hooks
3. Use the exhaustive-deps ESLint rule to catch missing dependencies in useEffect

React Hooks make your code more reusable, easier to test, and more maintainable. Start using them in your next project!',
    'published',
    1,
    1,
    'Getting Started with React Hooks: A Complete Guide',
    'Learn React Hooks fundamentals including useState, useEffect, and custom hooks. Complete guide with examples and best practices.',
    NOW(),
    NOW(),
    8,
    TRUE,
    'UTC',
    0,
    0
);

-- Post 2: Understanding JavaScript Closures
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'Understanding JavaScript Closures: The Complete Guide',
    'understanding-javascript-closures-complete-guide',
    'Closures are one of the most important concepts in JavaScript. Master closures to write better, more efficient code and ace your interviews.',
    'Closures are a fundamental concept in JavaScript that every developer should understand. They''re powerful, elegant, and used everywhere in modern JavaScript code.

## What is a Closure?

A closure is a function that has access to variables in its outer (enclosing) lexical scope, even after the outer function has returned. In simpler terms, a closure gives you access to an outer function''s scope from an inner function.

```javascript
function outerFunction(x) {
  // Outer function''s variable
  const outerVariable = x;

  // Inner function (closure)
  function innerFunction(y) {
    console.log(outerVariable + y);
  }

  return innerFunction;
}

const closure = outerFunction(10);
closure(5); // Output: 15
```

## How Closures Work

When a function is created, it captures the variables from its surrounding scope. This "captured" environment persists even after the outer function has finished executing.

```javascript
function createCounter() {
  let count = 0;
  
  return function() {
    count++;
    return count;
  };
}

const counter1 = createCounter();
const counter2 = createCounter();

console.log(counter1()); // 1
console.log(counter1()); // 2
console.log(counter2()); // 1 (independent closure)
```

## Common Use Cases

### 1. Data Privacy

Closures allow you to create private variables:

```javascript
function createBankAccount(initialBalance) {
  let balance = initialBalance; // Private variable

  return {
    deposit: function(amount) {
      balance += amount;
      return balance;
    },
    withdraw: function(amount) {
      if (amount <= balance) {
        balance -= amount;
        return balance;
      }
      return "Insufficient funds";
    },
    getBalance: function() {
      return balance;
    }
  };
}
```

### 2. Function Factories

Create specialized functions:

```javascript
function multiplyBy(multiplier) {
  return function(number) {
    return number * multiplier;
  };
}

const double = multiplyBy(2);
const triple = multiplyBy(3);

console.log(double(5)); // 10
console.log(triple(5)); // 15
```

### 3. Event Handlers and Callbacks

Closures are essential for event handlers:

```javascript
function setupButton(buttonId, message) {
  const button = document.getElementById(buttonId);
  
  button.addEventListener(''click'', function() {
    alert(message); // Closure captures ''message''
  });
}
```

## Common Pitfalls

### Loop Variable Capture

A common mistake when using closures in loops:

```javascript
// Wrong way
for (var i = 0; i < 3; i++) {
  setTimeout(function() {
    console.log(i); // Prints 3, 3, 3
  }, 1000);
}

// Correct way with let
for (let i = 0; i < 3; i++) {
  setTimeout(function() {
    console.log(i); // Prints 0, 1, 2
  }, 1000);
}
```

## Performance Considerations

Closures can lead to memory leaks if not used carefully. Variables captured in closures are not garbage collected as long as the closure exists.

Understanding closures is crucial for mastering JavaScript. They''re used in modules, callbacks, event handlers, and many design patterns.',
    'published',
    1,
    1,
    'Understanding JavaScript Closures: The Complete Guide',
    'Master JavaScript closures with examples, use cases, and common pitfalls. Essential knowledge for every JavaScript developer.',
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    10,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 3: CSS Grid vs Flexbox
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'CSS Grid vs Flexbox: When to Use Which',
    'css-grid-vs-flexbox-when-to-use-which',
    'CSS Grid and Flexbox are both powerful layout tools, but they serve different purposes. Learn when to use each one for optimal layouts.',
    'CSS Grid and Flexbox are two of the most powerful layout systems in CSS. While they can sometimes be used interchangeably, each has its strengths and ideal use cases.

## The Fundamental Difference

**Flexbox** is designed for one-dimensional layouts (either rows OR columns), while **CSS Grid** is designed for two-dimensional layouts (rows AND columns simultaneously).

## When to Use Flexbox

Flexbox excels at:

### 1. Component-Level Layouts

Use Flexbox for components like navigation bars, card layouts, and form controls:

```css
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
```

### 2. Content Distribution

Perfect for distributing space within a container:

```css
.card {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
```

### 3. Alignment

Excellent for centering content:

```css
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
```

## When to Use CSS Grid

Grid is ideal for:

### 1. Page-Level Layouts

Create complex page structures:

```css
.layout {
  display: grid;
  grid-template-columns: 200px 1fr 200px;
  grid-template-rows: auto 1fr auto;
  grid-template-areas:
    "header header header"
    "sidebar main aside"
    "footer footer footer";
}
```

### 2. Two-Dimensional Layouts

When you need control over both rows and columns:

```css
.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}
```

### 3. Overlapping Elements

Grid makes overlapping elements easy:

```css
.card {
  display: grid;
  grid-template-areas: "overlay";
}

.card > * {
  grid-area: overlay;
}
```

## Using Both Together

You can and should use both! Grid for the overall layout, Flexbox for components:

```css
.page {
  display: grid;
  grid-template-columns: 1fr 3fr;
  gap: 20px;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
```

## Quick Decision Guide

- **Use Flexbox** when: aligning items in one direction, distributing space within a component, or creating flexible component layouts
- **Use Grid** when: creating page layouts, working with two-dimensional layouts, or needing precise control over both rows and columns
- **Use Both** when: building complex layouts where Grid handles the structure and Flexbox handles component internals

Both tools are essential in modern CSS. Understanding when to use each will make you a more effective front-end developer.',
    'published',
    1,
    1,
    'CSS Grid vs Flexbox: When to Use Which',
    'Learn when to use CSS Grid vs Flexbox. Complete guide with examples and decision-making framework for choosing the right layout tool.',
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    7,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 4: TypeScript Basics
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'TypeScript Basics: Type Safety for JavaScript Developers',
    'typescript-basics-type-safety-javascript-developers',
    'TypeScript adds static typing to JavaScript, catching errors before runtime. Learn the fundamentals and start writing more robust code.',
    'TypeScript is a superset of JavaScript that adds static type definitions. It compiles to plain JavaScript but provides powerful tooling and type safety.

## Why TypeScript?

TypeScript offers several advantages:

1. **Early Error Detection**: Catch bugs during development, not in production
2. **Better IDE Support**: Autocomplete, refactoring, and navigation
3. **Self-Documenting Code**: Types serve as inline documentation
4. **Easier Refactoring**: Confident code changes with type checking

## Basic Types

TypeScript provides several basic types:

```typescript
// Primitive types
let name: string = "John";
let age: number = 30;
let isActive: boolean = true;

// Arrays
let numbers: number[] = [1, 2, 3];
let names: Array<string> = ["Alice", "Bob"];

// Any (use sparingly)
let value: any = "can be anything";

// Void (for functions that return nothing)
function logMessage(): void {
  console.log("Hello");
}
```

## Functions

Type annotations for function parameters and return types:

```typescript
function add(a: number, b: number): number {
  return a + b;
}

// Optional parameters
function greet(name: string, title?: string): string {
  return title ? `${title} ${name}` : name;
}

// Default parameters
function multiply(x: number, y: number = 1): number {
  return x * y;
}
```

## Interfaces

Define the shape of objects:

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  age?: number; // Optional property
}

function createUser(user: User): User {
  return user;
}

const newUser: User = {
  id: 1,
  name: "John Doe",
  email: "john@example.com"
};
```

## Classes

TypeScript enhances JavaScript classes with access modifiers:

```typescript
class Person {
  private name: string;
  public age: number;
  protected email: string;

  constructor(name: string, age: number, email: string) {
    this.name = name;
    this.age = age;
    this.email = email;
  }

  public getName(): string {
    return this.name;
  }
}
```

## Generics

Create reusable, type-safe components:

```typescript
function identity<T>(arg: T): T {
  return arg;
}

let output = identity<string>("myString");
let numberOutput = identity<number>(42);
```

## Type Inference

TypeScript can often infer types automatically:

```typescript
let x = 10; // TypeScript infers: number
let y = "hello"; // TypeScript infers: string

// No need to explicitly type
function add(a: number, b: number) {
  return a + b; // Return type inferred as number
}
```

## Getting Started

1. Install TypeScript: `npm install -g typescript`
2. Create a `tsconfig.json` file
3. Write `.ts` files instead of `.js`
4. Compile: `tsc filename.ts`

TypeScript is a powerful tool that can significantly improve your JavaScript development experience. Start with the basics and gradually adopt more advanced features.',
    'published',
    1,
    1,
    'TypeScript Basics: Type Safety for JavaScript Developers',
    'Learn TypeScript fundamentals: types, interfaces, classes, and generics. Start writing type-safe JavaScript code today.',
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    9,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 5: RESTful API Design
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'RESTful API Design: Best Practices and Principles',
    'restful-api-design-best-practices-principles',
    'Design APIs that are intuitive, maintainable, and follow REST principles. Learn best practices for building production-ready REST APIs.',
    'REST (Representational State Transfer) is an architectural style for designing networked applications. A well-designed REST API is intuitive, maintainable, and follows established conventions.

## REST Principles

### 1. Stateless

Each request from a client must contain all the information needed to process it. The server should not store any client context between requests.

### 2. Resource-Based URLs

URLs should represent resources, not actions:

```
✅ Good:  GET /api/users/123
❌ Bad:   GET /api/getUser?id=123
```

### 3. HTTP Methods

Use appropriate HTTP methods:

- **GET**: Retrieve a resource
- **POST**: Create a new resource
- **PUT**: Update/replace a resource
- **PATCH**: Partially update a resource
- **DELETE**: Remove a resource

## URL Design Best Practices

### Use Nouns, Not Verbs

```
✅ /api/users
✅ /api/posts/123/comments
❌ /api/getUsers
❌ /api/createPost
```

### Use Plural Nouns

```
✅ /api/users
✅ /api/posts
❌ /api/user
```

### Use Hierarchical Structure

```
✅ /api/users/123/posts
✅ /api/posts/456/comments
```

### Use Query Parameters for Filtering

```
✅ /api/posts?status=published&limit=10
✅ /api/users?role=admin&sort=name
```

## HTTP Status Codes

Use appropriate status codes:

- **200 OK**: Successful GET, PUT, PATCH
- **201 Created**: Successful POST
- **204 No Content**: Successful DELETE
- **400 Bad Request**: Invalid request
- **401 Unauthorized**: Authentication required
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource doesn''t exist
- **500 Internal Server Error**: Server error

## Response Format

### Consistent Structure

```json
{
  "data": {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

### Error Responses

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid email format",
    "details": {
      "field": "email",
      "value": "invalid-email"
    }
  }
}
```

## Versioning

Version your API:

```
/api/v1/users
/api/v2/users
```

Or use headers:

```
Accept: application/vnd.api+json;version=2
```

## Pagination

Implement pagination for list endpoints:

```json
{
  "data": [...],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 150,
    "total_pages": 8
  }
}
```

## Security Best Practices

1. **Use HTTPS**: Always encrypt data in transit
2. **Authentication**: Implement proper authentication (JWT, OAuth)
3. **Rate Limiting**: Prevent abuse
4. **Input Validation**: Validate and sanitize all inputs
5. **CORS**: Configure Cross-Origin Resource Sharing properly

## Documentation

Document your API thoroughly:

- Use OpenAPI/Swagger
- Provide examples
- Document error responses
- Include authentication requirements

A well-designed REST API is a joy to use and maintain. Follow these principles to create APIs that developers will love.',
    'published',
    1,
    1,
    'RESTful API Design: Best Practices and Principles',
    'Master RESTful API design with best practices for URLs, HTTP methods, status codes, versioning, and security.',
    DATE_SUB(NOW(), INTERVAL 10 DAY),
    DATE_SUB(NOW(), INTERVAL 10 DAY),
    12,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 6: Git Workflow Strategies
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'Git Workflow Strategies: Git Flow vs GitHub Flow',
    'git-workflow-strategies-git-flow-vs-github-flow',
    'Choose the right Git workflow for your team. Compare Git Flow, GitHub Flow, and other strategies to find what works best.',
    'Choosing the right Git workflow is crucial for team collaboration and code quality. Different workflows suit different team sizes and project requirements.

## Git Flow

Git Flow is a branching model designed around project releases. It uses multiple long-lived branches.

### Branch Structure

- **main/master**: Production-ready code
- **develop**: Integration branch for features
- **feature/**: New features
- **release/**: Preparing releases
- **hotfix/**: Critical production fixes

### Workflow

```bash
# Start a new feature
git checkout develop
git checkout -b feature/new-feature

# Work on feature, commit changes
git add .
git commit -m "Add new feature"

# Merge back to develop
git checkout develop
git merge feature/new-feature
git branch -d feature/new-feature

# Create release branch
git checkout -b release/1.0.0
# Fix bugs, update version numbers
git checkout main
git merge release/1.0.0
git tag -a v1.0.0
```

### Pros

- Clear separation of concerns
- Good for projects with scheduled releases
- Supports multiple versions in production

### Cons

- More complex than simpler workflows
- Can be overkill for small teams
- Requires discipline to maintain

## GitHub Flow

GitHub Flow is a simpler workflow with just two branches: main and feature branches.

### Branch Structure

- **main**: Always deployable
- **feature/**: Short-lived feature branches

### Workflow

```bash
# Create feature branch from main
git checkout main
git pull origin main
git checkout -b feature/new-feature

# Make changes and commit
git add .
git commit -m "Add feature"

# Push and create pull request
git push origin feature/new-feature
# Create PR on GitHub, get review, merge

# Deploy from main
git checkout main
git pull origin main
# Deploy
```

### Pros

- Simple and easy to understand
- Fast iteration
- Great for continuous deployment
- Works well for small to medium teams

### Cons

- Less structure for complex release cycles
- All features go directly to production
- May need additional branches for releases

## GitLab Flow

GitLab Flow combines Git Flow and GitHub Flow with environment branches.

### Branch Structure

- **main**: Development branch
- **pre-production**: Staging environment
- **production**: Production environment
- **feature/**: Feature branches

### Workflow

Features merge to main, then flow through pre-production to production.

## Choosing the Right Workflow

### Use Git Flow if:

- You have scheduled releases
- You need to support multiple versions
- You have a large team
- You need strict release management

### Use GitHub Flow if:

- You deploy continuously
- You have a small to medium team
- You want simplicity
- You can deploy features immediately

### Use GitLab Flow if:

- You need environment-specific branches
- You want a middle ground between Git Flow and GitHub Flow
- You need staging environments

## Best Practices (All Workflows)

1. **Keep branches short-lived**: Merge frequently
2. **Write clear commit messages**: Follow conventional commits
3. **Use pull/merge requests**: Code review is essential
4. **Protect main branch**: Require reviews and CI checks
5. **Tag releases**: Mark important milestones

The best workflow is the one your team will actually use. Start simple and evolve as needed.',
    'published',
    1,
    1,
    'Git Workflow Strategies: Git Flow vs GitHub Flow',
    'Compare Git Flow, GitHub Flow, and GitLab Flow. Choose the right Git workflow strategy for your team and project.',
    DATE_SUB(NOW(), INTERVAL 12 DAY),
    DATE_SUB(NOW(), INTERVAL 12 DAY),
    11,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 7: Docker Basics
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'Docker Basics: Containerization for Developers',
    'docker-basics-containerization-developers',
    'Learn Docker fundamentals: containers, images, Dockerfile, and docker-compose. Start containerizing your applications today.',
    'Docker has revolutionized how we develop, ship, and run applications. It packages applications and their dependencies into containers that run consistently across any environment.

## What is Docker?

Docker is a platform for developing, shipping, and running applications using containerization. Containers package an application with all its dependencies, ensuring it runs the same way everywhere.

## Key Concepts

### Images

An image is a read-only template for creating containers. Think of it as a blueprint.

```bash
# Pull an image
docker pull nginx

# List images
docker images

# Remove an image
docker rmi nginx
```

### Containers

A container is a running instance of an image.

```bash
# Run a container
docker run nginx

# Run in detached mode
docker run -d nginx

# List running containers
docker ps

# Stop a container
docker stop <container_id>

# Remove a container
docker rm <container_id>
```

## Dockerfile

A Dockerfile defines how to build an image:

```dockerfile
# Use an official base image
FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application code
COPY . .

# Expose port
EXPOSE 3000

# Run the application
CMD ["npm", "start"]
```

### Building an Image

```bash
# Build from Dockerfile
docker build -t my-app:latest .

# Tag an image
docker tag my-app:latest my-app:v1.0
```

## Docker Compose

Docker Compose manages multi-container applications:

```yaml
version: ''3.8''
services:
  web:
    build: .
    ports:
      - "3000:3000"
    environment:
      - NODE_ENV=production
    depends_on:
      - db

  db:
    image: postgres:15
    environment:
      - POSTGRES_DB=myapp
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=password
    volumes:
      - db-data:/var/lib/postgresql/data

volumes:
  db-data:
```

Run with:

```bash
docker-compose up
docker-compose down
```

## Common Commands

```bash
# View logs
docker logs <container_id>

# Execute command in running container
docker exec -it <container_id> /bin/bash

# Copy files to/from container
docker cp <container_id>:/path/to/file ./local/path

# Inspect container
docker inspect <container_id>

# View resource usage
docker stats
```

## Volumes

Volumes persist data outside containers:

```bash
# Create a volume
docker volume create my-volume

# Use a volume
docker run -v my-volume:/data nginx

# List volumes
docker volume ls
```

## Networks

Containers can communicate through networks:

```bash
# Create a network
docker network create my-network

# Run container on network
docker run --network my-network nginx
```

## Best Practices

1. **Use .dockerignore**: Exclude unnecessary files
2. **Multi-stage builds**: Reduce image size
3. **Don''t run as root**: Use non-root users
4. **Layer caching**: Order Dockerfile commands efficiently
5. **Keep images small**: Use alpine variants when possible

## Dockerfile Optimization

```dockerfile
# Multi-stage build example
FROM node:18 AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM node:18-alpine
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY package*.json ./
EXPOSE 3000
CMD ["node", "dist/index.js"]
```

Docker simplifies deployment and ensures consistency across environments. Start containerizing your applications today!',
    'published',
    1,
    1,
    'Docker Basics: Containerization for Developers',
    'Learn Docker fundamentals: images, containers, Dockerfile, and docker-compose. Start containerizing applications with this complete guide.',
    DATE_SUB(NOW(), INTERVAL 15 DAY),
    DATE_SUB(NOW(), INTERVAL 15 DAY),
    13,
    FALSE,
    'UTC',
    0,
    0
);

-- Post 8: Database Indexing
INSERT INTO `posts` (
    `title`, `slug`, `excerpt`, `body`, `status`, `primary_category_id`, 
    `author_id`, `meta_title`, `meta_description`, `publish_date`, `published_at`,
    `reading_time_minutes`, `featured`, `timezone`, `views_count`, `comments_count`
) VALUES (
    'Database Indexing: Optimize Your Queries',
    'database-indexing-optimize-queries',
    'Understanding database indexes is crucial for performance. Learn how indexes work and when to use them effectively.',
    'Database indexes are one of the most important tools for optimizing query performance. Understanding how they work can dramatically improve your application''s speed.

## What is an Index?

An index is a data structure that improves the speed of data retrieval operations on a database table. Think of it like an index in a book - instead of reading every page, you can jump directly to the relevant section.

## How Indexes Work

Without an index, the database must scan every row (full table scan) to find matching records. With an index, the database can quickly locate the data.

### B-Tree Index

The most common index type is a B-Tree (Balanced Tree):

```
        [50]
       /    \
    [25]    [75]
   /   \    /   \
[10] [30] [60] [90]
```

This structure allows O(log n) search time instead of O(n).

## Creating Indexes

### Single Column Index

```sql
CREATE INDEX idx_email ON users(email);
```

### Composite Index

```sql
CREATE INDEX idx_name_email ON users(last_name, first_name);
```

### Unique Index

```sql
CREATE UNIQUE INDEX idx_username ON users(username);
```

## When to Use Indexes

### Good Candidates

- **Foreign keys**: Frequently used in JOINs
- **Columns in WHERE clauses**: Filter conditions
- **Columns in ORDER BY**: Sorting operations
- **Columns in JOIN conditions**: Table relationships

### When NOT to Index

- **Small tables**: Overhead may exceed benefits
- **Frequently updated columns**: Indexes slow down writes
- **Columns with low cardinality**: Few unique values
- **Columns rarely used in queries**: Unnecessary overhead

## Index Types

### Primary Key Index

Automatically created for primary keys:

```sql
CREATE TABLE users (
  id INT PRIMARY KEY,  -- Automatically indexed
  name VARCHAR(100)
);
```

### Secondary Index

Manually created indexes:

```sql
CREATE INDEX idx_created_at ON posts(created_at);
```

### Composite Index

Multiple columns:

```sql
CREATE INDEX idx_status_date ON posts(status, created_at);
```

**Order matters!** The leftmost columns are most important.

## Index Strategies

### Covering Index

An index that contains all columns needed for a query:

```sql
-- Query
SELECT id, name, email FROM users WHERE email = ''test@example.com'';

-- Covering index
CREATE INDEX idx_email_covering ON users(email, id, name);
```

### Partial Index

Index only a subset of rows:

```sql
CREATE INDEX idx_active_users ON users(email) WHERE status = ''active'';
```

## Monitoring Index Usage

### MySQL

```sql
-- Check index usage
SHOW INDEX FROM users;

-- Analyze query
EXPLAIN SELECT * FROM users WHERE email = ''test@example.com'';
```

### PostgreSQL

```sql
-- Check index usage
SELECT * FROM pg_stat_user_indexes;

-- Analyze query
EXPLAIN ANALYZE SELECT * FROM users WHERE email = ''test@example.com'';
```

## Common Mistakes

1. **Over-indexing**: Too many indexes slow down writes
2. **Wrong column order**: Composite index order matters
3. **Ignoring query patterns**: Index what you actually query
4. **Not maintaining indexes**: Rebuild periodically

## Best Practices

1. **Index foreign keys**: Essential for JOIN performance
2. **Use EXPLAIN**: Understand query execution plans
3. **Monitor performance**: Track slow queries
4. **Regular maintenance**: Rebuild indexes as needed
5. **Test changes**: Measure before and after

## Example: Optimizing a Query

```sql
-- Slow query (no index)
SELECT * FROM posts 
WHERE status = ''published'' 
  AND category_id = 5 
ORDER BY created_at DESC 
LIMIT 10;

-- Add composite index
CREATE INDEX idx_status_category_date 
ON posts(status, category_id, created_at DESC);

-- Now the query uses the index efficiently
```

Proper indexing is crucial for database performance. Start by indexing foreign keys and frequently queried columns, then optimize based on your actual query patterns.',
    'published',
    1,
    1,
    'Database Indexing: Optimize Your Queries',
    'Master database indexing: B-Tree indexes, composite indexes, covering indexes, and best practices for query optimization.',
    DATE_SUB(NOW(), INTERVAL 18 DAY),
    DATE_SUB(NOW(), INTERVAL 18 DAY),
    14,
    FALSE,
    'UTC',
    0,
    0
);


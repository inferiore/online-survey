# Laravel Survey Management Application

A comprehensive survey management system built with Laravel with modern web technologies.

## 🚀 Features

- **Survey Management**: Create, edit, list, and manage surveys
- **Question Management**: Create various question types (rating, comment-only, multiple-choice)
- **Mass Operations**: Bulk assign questions to surveys and delete multiple questions
- **Public Survey Taking**: User-friendly survey forms for respondents
- **Advanced Filtering**: Filter surveys and questions with pagination
- **Responsive Design**: Built with Tailwind CSS for modern UI/UX
- **Docker Support**: Fully containerized development environment
- **Comprehensive Testing**: Feature tests with 95%+ coverage

## 🏗️ Tech Stack

- **Backend**: Laravel 10 (PHP 8.2+)
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL 8.0
- **Build Tools**: Vite for asset compilation
- **Containerization**: Docker & Docker Compose
- **Testing**: PHPUnit with RefreshDatabase

## 📊 Database Schema

### Core Tables

```sql
surveys
├── id (BIGINT, Primary Key)
├── name (VARCHAR(255))
├── status (ENUM: 'created', 'online', 'finished')
├── created_by_id (BIGINT, Foreign Key)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

questions
├── id (BIGINT, Primary Key)
├── name (VARCHAR(255))
├── question_text (TEXT)
├── question_type (ENUM: 'rating', 'comment-only', 'multiple-choice')
├── created_by_id (BIGINT, Foreign Key)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

question_survey (Pivot Table)
├── survey_id (BIGINT, Foreign Key)
├── question_id (BIGINT, Foreign Key)
├── created_by_id (BIGINT, Foreign Key)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
└── UNIQUE(survey_id, question_id)

survey_responses
├── id (BIGINT, Primary Key)
├── survey_id (BIGINT, Foreign Key)
├── question_id (BIGINT, Foreign Key)
├── response_value (TEXT)
├── submitted_at (TIMESTAMP)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

## 🛠️ Local Setup

### Prerequisites

- Docker Desktop
- Git

### Quick Start

1. **Clone the repository**
   ```bash
   git clone git@github.com:inferiore/online-survey.git
   cd online-survey
   ```
   
2. **Build and Start Services**
   ```bash
   # Build containers
   docker-compose build --no-cache
   
   # Start services
   docker-compose up -d
   ```

3. **Access the Application**
   - **Web Application**: http://localhost:8000
   - **MySQL Database**: localhost:3306

## 🧪 Testing

### Run All Tests
```bash
# Local environment
php artisan test
```

### Test Categories

- **Survey Management Tests** (12 tests)
  - CRUD operations
  - Validation
  - Filtering and pagination

- **Question Management Tests** (15 tests)
  - CRUD operations
  - Mass operations
  - Question type handling

- **Survey Taking Tests** (11 tests)
  - Public survey access
  - Response submission
  - Status-based restrictions

## 🛣️ API Routes

### Survey Management Routes
```
GET     /surveys              # List all surveys
GET     /surveys/create       # Show create form
POST    /surveys              # Store new survey
GET     /surveys/{survey}     # Show survey details
GET     /surveys/{survey}/edit # Show edit form
PUT     /surveys/{survey}     # Update survey
DELETE  /surveys/{survey}     # Delete survey
```

### Question Management Routes
```
GET     /questions              # List all questions
GET     /questions/create       # Show create form
POST    /questions              # Store new question
GET     /questions/{question}   # Show question details
GET     /questions/{question}/edit # Show edit form
PUT     /questions/{question}   # Update question
DELETE  /questions/{question}   # Delete question
GET     /questions/mass-assign  # Mass assignment interface
POST    /questions/assign       # Bulk assign questions
POST    /questions/mass-delete  # Bulk delete questions
```

### Public Survey Routes
```
GET     /survey/{survey}        # Take survey form
POST    /survey/{survey}/submit # Submit survey responses
```


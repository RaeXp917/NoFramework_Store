# Student Supplies Store (Pen & Panic)

A simple full-stack e-shop web application developed as a student project. It allows users to browse and search for products, view them in a paginated list, sort them by price or name, and add items to a shopping cart. Orders can be placed without a login system to keep things simple and focused on functionality.

**Languages**: The app includes support for two languages – Greek and English.

The project includes an admin panel for basic product management (add, edit, delete), viewing customer orders, and analyzing purchase trends with a chart showing the most purchased products.

This e-shop was built from scratch without using any frameworks, showcasing a solid understanding of full-stack development and database integration using PHP, MariaDB, and Docker.

---

## Table of Contents

*   [Features](#features)
*   [Technology Stack](#technology-stack)
*   [GIF Preview](#gif-preview)
*   [Screenshots](#screenshots)
*   [Getting Started (Docker Setup)](#getting-started-docker-setup)
    *   [Installation & Running](#installation--running)
    *   [Accessing the Application](#accessing-the-application)
    *   [Accessing the Admin Panel](#accessing-the-admin-panel)
    *   [Stopping the Application](#stopping-the-application)

---

## Features

*   Product listing with images and pricing
*   Search products by name
*   Pagination for product listings
*   Sorting by price or name (on the customer-facing side)
*   Shopping cart functionality
*   Place orders (no login required)
*   Multi-language support (English/Greek)
*   **Admin Panel:**
    *   Secure login (password in `admin_pass.txt`)
    *   Product management (Add, Edit, Delete products)
    *   View placed customer orders
    *   **Sortable product list** (sort by ID, name, price, or category for easy management)
    *   **Sales analysis graph** showing the most frequently purchased products

---

## Technology Stack

*   **Frontend:** HTML, CSS (Bootstrap), JavaScript (including Chart.js for the admin graph)
*   **Backend:** PHP 8.2 (Vanilla)
*   **Database:** MySQL

---

## GIF Preview

*(Make sure the GIF path is correct relative to the README.md file)*
![GIF Demo](student-supplies-store/assets/store.gif)

---

## Screenshots

*(Make sure the image paths are correct relative to the README.md file)*

### Homepage
![Homepage Screenshot](student-supplies-store/assets/homepage.JPG)
![Homepage Screenshot](student-supplies-store/assets/homepage_2.JPG)

### Shopping Cart
![Shopping cart Screenshot](student-supplies-store/assets/shoping_cart.JPG)

### Admin Panel
![Admin Screenshot](student-supplies-store/assets/admin_login.JPG)
![Admin Screenshot](student-supplies-store/assets/main_page_admin.JPG)
![Admin Screenshot](student-supplies-store/assets/manage_products.JPG)
![Admin Screenshot](student-supplies-store/assets/add_new_product.JPG)
![Admin Screenshot](student-supplies-store/assets/view_order_panel.JPG)
![Admin Screenshot](student-supplies-store/assets/view_panel.JPG)

---

## Getting Started (Docker Setup)

*   [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.
*   [Git](https://git-scm.com/downloads) installed (for cloning the repository).

### Installation & Running

1.  **Clone the Repository:**
    Open your terminal or command prompt and run:
    ```bash
    git clone https://github.com/RaeXp917/eshop-project.git
    ```
    *(Assuming this is your correct repository URL)*

2.  **Navigate to Project Directory:**
    ```bash
    cd eshop-project
    ```
    *(Or `cd student-supplies-store` if the repo root doesn't match the folder name)*

3.  **Ensure `database.sql` is Present:**
    Verify that the `database.sql` file exists in the project root directory. This file contains the necessary database schema and initial data.

4.  **Build and Start the Docker Containers:**
    Run the following command in the project directory:
    ```bash
    docker-compose up --build -d
    ```
    *   This command builds the PHP/Apache image based on the `Dockerfile` and starts the web server and database containers defined in `docker-compose.yml`.
    *   The first time you run this, Docker will download the necessary base images (PHP and MariaDB), which might take a few minutes.
    *   The `-d` flag runs the containers in the background.

### Accessing the Application

*   Once the containers are running (the `docker-compose up` command finishes), open your web browser and navigate to:
    `http://localhost:8080`
*   You should see the homepage of the e-shop!

### Accessing the Admin Panel

*   The admin panel requires a password for access.
*   You can find the admin password inside the `admin_pass.txt` file located in the project's root directory.

### Stopping the Application

To stop the running Docker containers, navigate back to the project directory in your terminal and run:

```bash
docker-compose down

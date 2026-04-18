# 🩸 SmartBlood Emergency Response System

## About the Project

SmartBlood is an advanced DBMS-based web application developed to bridge the critical gap between hospitals facing emergencies and active blood donors. Moving beyond a traditional static directory, this platform introduces an intelligent, location-aware matching engine to reduce response times during life-threatening situations. 

This project integrates complex mathematical logic directly into the database query engine:
- **Spatial Calculations:** Implements the Haversine formula in MySQL to calculate real physical distances between hospitals and donors.
- **Dynamic Ranking Algorithm:** Ranks perfectly-matched donors based on a combined Proximity Score and Recency Score (activity tracking).
- **Automated Health Checks:** Strictly enforces a 90-day safe-interval rule for donors before they can be matched again.

## 🔗 Repository

💻 **GitHub Repository:** [View Source Code](https://github.com/vireshnagthane/SmartBlood-Emergency-System)

---

## 📌 Project Overview

Traditional donor systems require administrators to manually scroll through lists of offline donors based on city text strings. **SmartBlood** revolutionizes this by allowing hospitals to trigger an **"Emergency Blast"**. The system instantly scans the database, executes the Haversine distance calculations, ignores donors on cooldown, and produces a highly targeted top-match list with simulated live SMS pings.

---


## ✨ Key Features

- **Location-Based Matchmaking:** MySQL Haversine distance formula tracking.
- **Urgency Matrix:** Requests classified by Low, Medium, and High (Life-Threatening) urgencies.
- **Live Emergency Tickers:** Real-time UI updates broadcasting high-urgency alerts publicly.
- **Donor Availability Toggles:** Users actively control their emergency "online" status.
- **Advanced Dashboard Tools:** Complete control room for hospital administrators.
- **Enterprise Security:** Password hashing and parameterized MySQLi queries protecting data.

---

## 🛠️ Technologies Used

- **Frontend:** HTML5, Vanilla CSS, Bootstrap 5, Vanilla JavaScript
- **Backend Logic:** PHP 8 with session management
- **Database:** Relational MySQL (utilizing Spatial & Mathematical operations)
- **Version Control:** GitHub

---

## Project Structure

Sai_hosptal/
│
├── screenshots/
├── database.sql
├── db.php
├── style.css
├── script.js
├── index.php
├── login.php
├── signup.php
├── dashboard.php
├── request_blood.php
├── emergency_search.php
├── hospital_register.php
├── donor_login.php
├── donor_register.php
├── donor_profile.php
└── logout.php




## What I Learned

Through this project, I learned:
- How to design and connect a database with a web application
- How frontend, backend, and database work together
- How to perform form handling using PHP
- How to store and fetch data from MySQL
- How to deploy a PHP project live
- How to present a project using GitHub

## Author
Viresh Nagthane  
B.Tech CSE  
Sanjivani College of Engineering

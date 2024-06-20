# Task Manager

A lightweight task management utility. Organize your tasks, assign priorities, mark them as completed.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Showcase](#showcase)

## Features

- Create new tasks
- Assign a priority level to them
- Mark them as completed
- Edit tasks
- Delete tasks

## Prerequisites

- XAMPP (ensure Apache and MySQL services are running)
- PHP (8.2 or higher)

## Installation

### Windows

- Make sure you have xampp installed, get it [here](https://www.apachefriends.org)
- Download a zip file of this repository
- Navigate to xampp's htdocs folder (usually at C:\xampp\htdocs)
- Make a new folder called "WebServer_xampp"
- Extract the contents of the zip into this folder
- Open xampp-control and start the services Apache and MySQL

### Linux

- Make sure you have xampp installed, get it [here](https://www.apachefriends.org)
- Create a new directory within the htdocs directory called "WebServer_xampp"
```bash
cd path/to/htdocs
mkdir WebServer_xampp
```

- Clone the github repository into the crated directory
```bash
git clone https://github.com/zakater5/TaskManager.git
```

- Navigate to your xampp installation directory (tipically /opt/lampp)
```bash
cd /opt/lampp
```

- Start apache and MySQL
```bash
sudo ./xampp startapache
sudo ./xampp startmysql
```

## Usage
You can access the website itself at its local ip if using in a local network or through localhost if using on same device.
By default the database has an account with the **username: admin and password: password**.
More account can be created in the phpmyadmin control panel.

## Showcase
![Welcome img](https://imgur.com/WAY2X69)
![Welcome img](https://imgur.com/yT0SITO)
![Welcome img](https://imgur.com/mfGUTpY)
